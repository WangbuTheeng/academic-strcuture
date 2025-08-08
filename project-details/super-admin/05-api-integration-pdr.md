# 🔌 API & Integration PDR
**Project Design Requirements - API Architecture & Third-Party Integrations**

## 📋 Overview

This PDR defines the API architecture and integration framework for the Super Admin system, enabling secure, scalable, and maintainable connections with external systems while preserving multi-tenant data isolation and security principles.

---

## 🎯 API Objectives

### Primary Goals
- **Secure API Access**: Robust authentication and authorization for API endpoints
- **Multi-Tenant Isolation**: Maintain data separation in API responses
- **Scalable Architecture**: Support high-volume API requests efficiently
- **Developer Experience**: Intuitive, well-documented API interfaces
- **Integration Flexibility**: Support various integration patterns and protocols

### API Principles
- **RESTful Design**: Follow REST architectural principles
- **Stateless Operations**: No server-side session dependencies
- **Consistent Responses**: Standardized response formats
- **Version Management**: Backward-compatible API versioning
- **Rate Limiting**: Protect against abuse and ensure fair usage

---

## 🏗️ API Architecture

### 1. API Structure & Versioning

#### URL Structure
```
Base URL: https://api.academicms.com/v1/

Super Admin Endpoints:
├── /v1/super-admin/schools
├── /v1/super-admin/analytics
├── /v1/super-admin/system
└── /v1/super-admin/audit

School-Specific Endpoints:
├── /v1/schools/{school_id}/students
├── /v1/schools/{school_id}/teachers
├── /v1/schools/{school_id}/classes
└── /v1/schools/{school_id}/exams
```

#### API Versioning Strategy
```php
class APIVersionManager
{
    private array $supportedVersions = ['v1', 'v2'];
    private string $defaultVersion = 'v1';
    private array $deprecatedVersions = [];
    
    public function resolveVersion(Request $request): string
    {
        // Check Accept header
        $acceptHeader = $request->header('Accept');
        if (preg_match('/application\/vnd\.academicms\.v(\d+)\+json/', $acceptHeader, $matches)) {
            $version = 'v' . $matches[1];
            if (in_array($version, $this->supportedVersions)) {
                return $version;
            }
        }
        
        // Check URL path
        $pathVersion = $request->segment(1);
        if (in_array($pathVersion, $this->supportedVersions)) {
            return $pathVersion;
        }
        
        // Check query parameter
        $queryVersion = $request->query('version');
        if ($queryVersion && in_array($queryVersion, $this->supportedVersions)) {
            return $queryVersion;
        }
        
        return $this->defaultVersion;
    }
}
```

### 2. Authentication & Authorization

#### API Authentication Methods
```php
class APIAuthenticationService
{
    public function authenticateRequest(Request $request): ?User
    {
        // Bearer Token Authentication
        if ($token = $this->extractBearerToken($request)) {
            return $this->authenticateWithToken($token);
        }
        
        // API Key Authentication
        if ($apiKey = $request->header('X-API-Key')) {
            return $this->authenticateWithAPIKey($apiKey);
        }
        
        // OAuth 2.0 Authentication
        if ($oauthToken = $this->extractOAuthToken($request)) {
            return $this->authenticateWithOAuth($oauthToken);
        }
        
        return null;
    }
    
    private function authenticateWithToken(string $token): ?User
    {
        $personalAccessToken = PersonalAccessToken::findToken($token);
        
        if (!$personalAccessToken || $personalAccessToken->expires_at < now()) {
            return null;
        }
        
        // Update last used timestamp
        $personalAccessToken->update(['last_used_at' => now()]);
        
        return $personalAccessToken->tokenable;
    }
    
    private function authenticateWithAPIKey(string $apiKey): ?User
    {
        $hashedKey = hash('sha256', $apiKey);
        
        $apiKeyRecord = APIKey::where('key_hash', $hashedKey)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();
            
        if (!$apiKeyRecord) {
            return null;
        }
        
        // Update usage statistics
        $apiKeyRecord->increment('usage_count');
        $apiKeyRecord->update(['last_used_at' => now()]);
        
        return $apiKeyRecord->user;
    }
}
```

#### API Key Management
```sql
CREATE TABLE api_keys (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    key_hash VARCHAR(255) NOT NULL UNIQUE,
    permissions JSON,
    rate_limit_per_minute INT DEFAULT 60,
    usage_count BIGINT DEFAULT 0,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_key_hash (key_hash),
    INDEX idx_user_active (user_id, is_active)
);
```

### 3. Rate Limiting & Throttling

#### Rate Limiting Implementation
```php
class APIRateLimiter
{
    public function checkRateLimit(Request $request, User $user): bool
    {
        $key = $this->generateRateLimitKey($request, $user);
        $limit = $this->getRateLimit($user);
        $window = 60; // 1 minute window
        
        $current = Cache::get($key, 0);
        
        if ($current >= $limit) {
            $this->logRateLimitExceeded($request, $user);
            return false;
        }
        
        Cache::put($key, $current + 1, $window);
        
        // Add rate limit headers to response
        $this->addRateLimitHeaders($limit, $current + 1, $window);
        
        return true;
    }
    
    private function getRateLimit(User $user): int
    {
        // Different limits based on user type and subscription
        if ($user->hasRole('super-admin')) {
            return 1000; // Higher limit for super admins
        }
        
        if ($user->subscription_tier === 'premium') {
            return 500;
        }
        
        return 100; // Default limit
    }
    
    private function addRateLimitHeaders(int $limit, int $used, int $window): void
    {
        response()->header('X-RateLimit-Limit', $limit);
        response()->header('X-RateLimit-Remaining', max(0, $limit - $used));
        response()->header('X-RateLimit-Reset', now()->addSeconds($window)->timestamp);
    }
}
```

---

## 📡 Super Admin API Endpoints

### 1. School Management API

#### School CRUD Operations
```php
class SuperAdminSchoolAPIController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $schools = School::query()
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->withCount(['users', 'students'])
            ->paginate($request->per_page ?? 15);
            
        return response()->json([
            'data' => SchoolResource::collection($schools->items()),
            'meta' => [
                'current_page' => $schools->currentPage(),
                'total' => $schools->total(),
                'per_page' => $schools->perPage()
            ]
        ]);
    }
    
    public function store(StoreSchoolRequest $request): JsonResponse
    {
        $school = $this->schoolService->createSchool($request->validated());
        
        return response()->json([
            'message' => 'School created successfully',
            'data' => new SchoolResource($school['school']),
            'credentials' => $school['credentials']
        ], 201);
    }
    
    public function show(School $school): JsonResponse
    {
        $school->load(['creator', 'statistics']);
        
        return response()->json([
            'data' => new SchoolDetailResource($school)
        ]);
    }
    
    public function update(UpdateSchoolRequest $request, School $school): JsonResponse
    {
        $school = $this->schoolService->updateSchool($school, $request->validated());
        
        return response()->json([
            'message' => 'School updated successfully',
            'data' => new SchoolResource($school)
        ]);
    }
    
    public function destroy(School $school): JsonResponse
    {
        $this->schoolService->deleteSchool($school);
        
        return response()->json([
            'message' => 'School deleted successfully'
        ]);
    }
}
```

#### API Resource Classes
```php
class SchoolResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'users_count' => $this->whenCounted('users'),
            'students_count' => $this->whenCounted('students'),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'links' => [
                'self' => route('api.super-admin.schools.show', $this->id),
                'update' => route('api.super-admin.schools.update', $this->id),
                'delete' => route('api.super-admin.schools.destroy', $this->id)
            ]
        ];
    }
}
```

### 2. Analytics & Reporting API

#### System Analytics Endpoints
```php
class SuperAdminAnalyticsAPIController extends Controller
{
    public function systemOverview(): JsonResponse
    {
        $overview = $this->analyticsService->getSystemOverview();
        
        return response()->json([
            'data' => [
                'schools' => [
                    'total' => $overview['total_schools'],
                    'active' => $overview['active_schools'],
                    'inactive' => $overview['inactive_schools'],
                    'suspended' => $overview['suspended_schools']
                ],
                'users' => [
                    'total' => $overview['total_users'],
                    'active_today' => $overview['active_users_today'],
                    'new_this_month' => $overview['new_users_this_month']
                ],
                'system_health' => $overview['system_health'],
                'performance_metrics' => $overview['performance_metrics']
            ]
        ]);
    }
    
    public function schoolGrowth(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'required|in:daily,weekly,monthly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);
        
        $growth = $this->analyticsService->getSchoolGrowth(
            $request->period,
            $request->start_date,
            $request->end_date
        );
        
        return response()->json(['data' => $growth]);
    }
    
    public function usageStatistics(Request $request): JsonResponse
    {
        $stats = $this->analyticsService->getUsageStatistics($request->all());
        
        return response()->json(['data' => $stats]);
    }
}
```

### 3. Audit & Compliance API

#### Audit Log Endpoints
```php
class SuperAdminAuditAPIController extends Controller
{
    public function auditLogs(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'action' => 'string',
            'user_id' => 'integer',
            'severity' => 'in:info,warning,error,critical'
        ]);
        
        $logs = AuditLog::query()
            ->when($request->start_date, fn($q, $date) => $q->where('timestamp', '>=', $date))
            ->when($request->end_date, fn($q, $date) => $q->where('timestamp', '<=', $date))
            ->when($request->action, fn($q, $action) => $q->where('action', 'like', "%{$action}%"))
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->severity, fn($q, $severity) => $q->where('severity', $severity))
            ->with('user')
            ->orderBy('timestamp', 'desc')
            ->paginate($request->per_page ?? 50);
            
        return response()->json([
            'data' => AuditLogResource::collection($logs->items()),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'total' => $logs->total(),
                'per_page' => $logs->perPage()
            ]
        ]);
    }
    
    public function complianceReport(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:ferpa,gdpr,general',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);
        
        $report = $this->complianceService->generateReport(
            $request->type,
            $request->start_date,
            $request->end_date
        );
        
        return response()->json(['data' => $report]);
    }
}
```

---

## 🔗 Third-Party Integrations

### 1. Webhook System

#### Webhook Implementation
```php
class WebhookService
{
    public function registerWebhook(array $data): Webhook
    {
        return Webhook::create([
            'url' => $data['url'],
            'events' => $data['events'],
            'secret' => $this->generateWebhookSecret(),
            'is_active' => true,
            'created_by' => auth()->id()
        ]);
    }
    
    public function triggerWebhook(string $event, array $payload): void
    {
        $webhooks = Webhook::where('is_active', true)
            ->whereJsonContains('events', $event)
            ->get();
            
        foreach ($webhooks as $webhook) {
            $this->sendWebhook($webhook, $event, $payload);
        }
    }
    
    private function sendWebhook(Webhook $webhook, string $event, array $payload): void
    {
        $signature = $this->generateSignature($webhook->secret, $payload);
        
        Http::withHeaders([
            'X-Webhook-Signature' => $signature,
            'X-Webhook-Event' => $event,
            'Content-Type' => 'application/json'
        ])->post($webhook->url, $payload);
        
        $webhook->increment('delivery_count');
        $webhook->update(['last_delivery_at' => now()]);
    }
}
```

#### Webhook Events
```php
// Available webhook events
$webhookEvents = [
    'school.created',
    'school.updated',
    'school.deleted',
    'school.activated',
    'school.deactivated',
    'school.suspended',
    'user.created',
    'user.updated',
    'user.deleted',
    'security.incident',
    'system.maintenance'
];
```

### 2. External System Integrations

#### SIS (Student Information System) Integration
```php
class SISIntegrationService
{
    public function syncStudentData(School $school, array $students): array
    {
        $results = [];
        
        foreach ($students as $studentData) {
            try {
                $student = $this->createOrUpdateStudent($school, $studentData);
                $results[] = [
                    'status' => 'success',
                    'student_id' => $student->id,
                    'external_id' => $studentData['external_id']
                ];
            } catch (Exception $e) {
                $results[] = [
                    'status' => 'error',
                    'error' => $e->getMessage(),
                    'external_id' => $studentData['external_id']
                ];
            }
        }
        
        return $results;
    }
    
    private function createOrUpdateStudent(School $school, array $data): Student
    {
        return Student::updateOrCreate(
            [
                'school_id' => $school->id,
                'external_id' => $data['external_id']
            ],
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'date_of_birth' => $data['date_of_birth'],
                'enrollment_date' => $data['enrollment_date'],
                'class_id' => $this->resolveClassId($school, $data['class_name'])
            ]
        );
    }
}
```

#### LMS (Learning Management System) Integration
```php
class LMSIntegrationService
{
    public function syncCourseData(School $school, array $courses): array
    {
        $results = [];
        
        foreach ($courses as $courseData) {
            try {
                $subject = $this->createOrUpdateSubject($school, $courseData);
                $results[] = [
                    'status' => 'success',
                    'subject_id' => $subject->id,
                    'external_id' => $courseData['external_id']
                ];
            } catch (Exception $e) {
                $results[] = [
                    'status' => 'error',
                    'error' => $e->getMessage(),
                    'external_id' => $courseData['external_id']
                ];
            }
        }
        
        return $results;
    }
    
    public function exportGrades(School $school, int $examId): array
    {
        $marks = Mark::where('school_id', $school->id)
            ->where('exam_id', $examId)
            ->with(['student', 'subject'])
            ->get();
            
        return $marks->map(function ($mark) {
            return [
                'student_external_id' => $mark->student->external_id,
                'course_external_id' => $mark->subject->external_id,
                'grade' => $mark->marks,
                'grade_points' => $mark->grade_points,
                'recorded_at' => $mark->created_at->toISOString()
            ];
        })->toArray();
    }
}
```

---

## 📊 API Documentation & Testing

### 1. OpenAPI Specification

#### API Documentation Generation
```php
class APIDocumentationGenerator
{
    public function generateOpenAPISpec(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Academic Management System API',
                'version' => '1.0.0',
                'description' => 'API for managing schools and academic data'
            ],
            'servers' => [
                ['url' => 'https://api.academicms.com/v1']
            ],
            'paths' => $this->generatePaths(),
            'components' => [
                'schemas' => $this->generateSchemas(),
                'securitySchemes' => $this->generateSecuritySchemes()
            ]
        ];
    }
    
    private function generatePaths(): array
    {
        return [
            '/super-admin/schools' => [
                'get' => [
                    'summary' => 'List schools',
                    'parameters' => [
                        ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Success', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/SchoolList']]]]
                    ]
                ],
                'post' => [
                    'summary' => 'Create school',
                    'requestBody' => ['content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateSchoolRequest']]]],
                    'responses' => [
                        '201' => ['description' => 'Created', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/School']]]]
                    ]
                ]
            ]
        ];
    }
}
```

### 2. API Testing Framework

#### Automated API Tests
```php
class SuperAdminAPITest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_list_schools()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');
        
        School::factory()->count(3)->create();
        
        $response = $this->actingAs($superAdmin, 'api')
            ->getJson('/api/v1/super-admin/schools');
            
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'name', 'status', 'created_at']
                ],
                'meta' => ['current_page', 'total', 'per_page']
            ]);
    }
    
    public function test_can_create_school()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');
        
        $schoolData = [
            'name' => 'Test School',
            'email' => 'test@school.edu',
            'phone' => '+1-555-0123',
            'address' => '123 Test St',
            'password' => 'SecurePassword123!'
        ];
        
        $response = $this->actingAs($superAdmin, 'api')
            ->postJson('/api/v1/super-admin/schools', $schoolData);
            
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'code', 'name'],
                'credentials' => ['school_id', 'school_password']
            ]);
            
        $this->assertDatabaseHas('schools', [
            'name' => 'Test School',
            'email' => 'test@school.edu'
        ]);
    }
}
```

---

## ✅ Implementation Checklist

### Core API Features
- [ ] RESTful API endpoints for school management
- [ ] API authentication and authorization
- [ ] Rate limiting and throttling
- [ ] API versioning system
- [ ] Comprehensive error handling
- [ ] Request/response validation

### Security Features
- [ ] API key management
- [ ] OAuth 2.0 support
- [ ] Request signing and verification
- [ ] CORS configuration
- [ ] API security headers
- [ ] Audit logging for API access

### Integration Features
- [ ] Webhook system implementation
- [ ] Third-party system connectors
- [ ] Data synchronization services
- [ ] Export/import capabilities
- [ ] Real-time event streaming
- [ ] Bulk operation support

### Documentation & Testing
- [ ] OpenAPI specification
- [ ] Interactive API documentation
- [ ] SDK generation
- [ ] Automated API testing
- [ ] Performance testing
- [ ] Integration testing

---

**Status**: 🟢 Implementation Complete
**Last Updated**: 2025-01-04
**Next Review**: 2025-02-04
