<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarksheetTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'template_type',
        'grading_scale_id',
        'institute_settings_id',
        'settings',
        'custom_css',
        'is_default',
        'is_active',
        'is_global'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    /**
     * Get the grading scale associated with this template.
     */
    public function gradingScale()
    {
        return $this->belongsTo(GradingScale::class);
    }

    /**
     * Get the institute settings associated with this template.
     */
    public function instituteSettings()
    {
        return $this->belongsTo(InstituteSettings::class);
    }

    /**
     * Get the marksheets that use this template.
     */
    public function marksheets()
    {
        return $this->hasMany(Marksheet::class, 'template_id');
    }

    /**
     * Scope to get the default template.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get global templates.
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    /**
     * Scope to get templates for a specific institute.
     */
    public function scopeForInstitute($query, $instituteId)
    {
        return $query->where('institute_settings_id', $instituteId);
    }

    /**
     * Scope to get templates available for a specific institute (global + institute-specific).
     */
    public function scopeAvailableForInstitute($query, $instituteId = null)
    {
        return $query->where(function($q) use ($instituteId) {
            $q->where('is_global', true);
            if ($instituteId) {
                $q->orWhere('institute_settings_id', $instituteId);
            }
        });
    }

    /**
     * Get the template type label.
     */
    public function getTypeLabel()
    {
        return match($this->template_type) {
            'modern' => 'Modern Design',
            'classic' => 'Classic Design',
            'minimal' => 'Minimal Design',
            'custom' => 'Custom Design',
            default => 'Unknown'
        };
    }

    /**
     * Get the table columns configuration based on exam structure.
     */
    public function getTableColumns($exam = null)
    {
        // If exam is provided, use its configuration for intelligent column detection
        if ($exam) {
            return $this->getIntelligentColumns($exam);
        }

        // Fallback to template settings
        $defaultColumns = [
            ['key' => 'subject', 'label' => 'Subject', 'width' => '25%', 'enabled' => true, 'order' => 1],
            ['key' => 'theory_marks', 'label' => 'Theory', 'width' => '10%', 'enabled' => $this->hasSetting('show_theory_practical'), 'order' => 2],
            ['key' => 'practical_marks', 'label' => 'Practical', 'width' => '10%', 'enabled' => $this->hasSetting('show_theory_practical'), 'order' => 3],
            ['key' => 'assessment_marks', 'label' => 'Assessment', 'width' => '10%', 'enabled' => $this->hasSetting('show_assessment_marks'), 'order' => 4],
            ['key' => 'total_marks', 'label' => 'Total', 'width' => '10%', 'enabled' => true, 'order' => 5],
            ['key' => 'grade', 'label' => 'Grade', 'width' => '8%', 'enabled' => true, 'order' => 6],
            ['key' => 'grade_points', 'label' => 'GP', 'width' => '8%', 'enabled' => $this->hasSetting('show_grade_points'), 'order' => 7],
            ['key' => 'attendance', 'label' => 'Attendance', 'width' => '8%', 'enabled' => $this->hasSetting('show_attendance'), 'order' => 8],
            ['key' => 'result', 'label' => 'Result', 'width' => '8%', 'enabled' => true, 'order' => 9],
            ['key' => 'rank', 'label' => 'Rank', 'width' => '8%', 'enabled' => $this->hasSetting('show_rank'), 'order' => 10],
        ];

        // Get custom columns from settings if available
        $customColumns = $this->settings['table_columns'] ?? $defaultColumns;

        // Sort by order and filter enabled columns
        return collect($customColumns)
            ->sortBy('order')
            ->filter(fn($col) => $col['enabled'])
            ->values()
            ->toArray();
    }

    /**
     * Get intelligent columns based on exam configuration.
     */
    public function getIntelligentColumns($exam)
    {
        $columns = [];
        $order = 1;

        // Subject column (always present)
        $columns[] = [
            'key' => 'subject',
            'label' => 'Subject',
            'width' => '25%',
            'enabled' => true,
            'order' => $order++,
            'type' => 'text'
        ];

        // Get total width available for marks columns
        $marksColumns = 0;
        if ($exam->theory_max && $exam->theory_max > 0) $marksColumns++;
        if ($exam->has_practical && $exam->practical_max && $exam->practical_max > 0) $marksColumns++;
        if ($exam->has_assessment && $exam->assess_max && $exam->assess_max > 0) $marksColumns++;

        // Calculate dynamic width for marks columns
        $marksWidth = $marksColumns > 0 ? (45 / $marksColumns) . '%' : '15%';

        // Theory marks (with max marks in header)
        if ($exam->theory_max && $exam->theory_max > 0) {
            $columns[] = [
                'key' => 'theory_marks',
                'label' => "Theory({$exam->theory_max})",
                'width' => $marksWidth,
                'enabled' => true,
                'order' => $order++,
                'type' => 'marks',
                'max_marks' => $exam->theory_max
            ];
        }

        // Practical marks (only if exam has practical component)
        if ($exam->has_practical && $exam->practical_max && $exam->practical_max > 0) {
            $columns[] = [
                'key' => 'practical_marks',
                'label' => "Practical({$exam->practical_max})",
                'width' => $marksWidth,
                'enabled' => true,
                'order' => $order++,
                'type' => 'marks',
                'max_marks' => $exam->practical_max
            ];
        }

        // Assessment marks (only if exam has assessment component)
        if ($exam->has_assessment && $exam->assess_max && $exam->assess_max > 0) {
            $columns[] = [
                'key' => 'assessment_marks',
                'label' => "Assessment({$exam->assess_max})",
                'width' => $marksWidth,
                'enabled' => true,
                'order' => $order++,
                'type' => 'marks',
                'max_marks' => $exam->assess_max
            ];
        }

        // Total marks (with max marks in header)
        $columns[] = [
            'key' => 'total_marks',
            'label' => "Total({$exam->max_marks})",
            'width' => '12%',
            'enabled' => true,
            'order' => $order++,
            'type' => 'total',
            'max_marks' => $exam->max_marks
        ];

        // Grade column
        $columns[] = [
            'key' => 'grade',
            'label' => 'Grade',
            'width' => '8%',
            'enabled' => true,
            'order' => $order++,
            'type' => 'grade'
        ];

        // Grade points (if enabled in template)
        if ($this->hasSetting('show_grade_points')) {
            $columns[] = [
                'key' => 'grade_points',
                'label' => 'GP',
                'width' => '6%',
                'enabled' => true,
                'order' => $order++,
                'type' => 'gpa'
            ];
        }

        // Result column
        $columns[] = [
            'key' => 'result',
            'label' => 'Result',
            'width' => '8%',
            'enabled' => true,
            'order' => $order++,
            'type' => 'result'
        ];

        // Additional optional columns
        if ($this->hasSetting('show_attendance')) {
            $columns[] = [
                'key' => 'attendance',
                'label' => 'Attendance',
                'width' => '8%',
                'enabled' => true,
                'order' => $order++,
                'type' => 'percentage'
            ];
        }

        if ($this->hasSetting('show_rank')) {
            $columns[] = [
                'key' => 'rank',
                'label' => 'Rank',
                'width' => '6%',
                'enabled' => true,
                'order' => $order++,
                'type' => 'number'
            ];
        }

        // Apply custom column order if set in template
        if (isset($this->settings['custom_column_order'])) {
            $columns = $this->applyCustomColumnOrder($columns, $this->settings['custom_column_order']);
        }

        return $columns;
    }

    /**
     * Apply custom column ordering from drag-and-drop interface.
     */
    private function applyCustomColumnOrder($columns, $customOrder)
    {
        $orderedColumns = [];
        $columnsByKey = collect($columns)->keyBy('key');

        foreach ($customOrder as $index => $columnKey) {
            if (isset($columnsByKey[$columnKey])) {
                $column = $columnsByKey[$columnKey];
                $column['order'] = $index + 1;
                $orderedColumns[] = $column;
            }
        }

        // Add any columns not in custom order at the end
        foreach ($columns as $column) {
            if (!in_array($column['key'], $customOrder)) {
                $column['order'] = count($orderedColumns) + 1;
                $orderedColumns[] = $column;
            }
        }

        return $orderedColumns;
    }

    /**
     * Get table style CSS based on template settings.
     */
    private function getTableStyleCSS()
    {
        $tableStyle = $this->settings['table_style'] ?? 'bordered';
        $highlightTotals = $this->settings['highlight_totals'] ?? true;

        $css = '';

        switch ($tableStyle) {
            case 'bordered':
                $css .= "
                .marksheet-table th,
                .marksheet-table td {
                    border: 1px solid #ddd;
                }
                ";
                break;

            case 'striped':
                $css .= "
                .marksheet-table th,
                .marksheet-table td {
                    border: 1px solid #ddd;
                }
                .marksheet-table tbody tr:nth-child(even) {
                    background-color: #f8f9fa;
                }
                ";
                break;

            case 'minimal':
                $css .= "
                .marksheet-table th {
                    border-bottom: 2px solid #ddd;
                    border-top: none;
                    border-left: none;
                    border-right: none;
                }
                .marksheet-table td {
                    border: none;
                    border-bottom: 1px solid #eee;
                }
                ";
                break;

            case 'modern':
                $headerColor = $this->settings['header_color'] ?? '#2563eb';
                $darkerColor = $this->darkenColor($headerColor, 20);
                $css .= "
                .marksheet-table {
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                .marksheet-table th,
                .marksheet-table td {
                    border: none;
                    border-bottom: 1px solid #e5e7eb;
                }
                .marksheet-table th {
                    background: linear-gradient(135deg, {$headerColor}, {$darkerColor});
                }
                ";
                break;
        }

        if ($highlightTotals) {
            $headerColor = $this->settings['header_color'] ?? '#2563eb';
            $css .= "
            .marksheet-table .total-cell {
                background-color: #e0f2fe !important;
                border-left: 3px solid {$headerColor};
            }
            ";
        }

        return $css;
    }

    /**
     * Darken a hex color by a percentage.
     */
    private function darkenColor($hex, $percent)
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r - ($r * $percent / 100)));
        $g = max(0, min(255, $g - ($g * $percent / 100)));
        $b = max(0, min(255, $b - ($b * $percent / 100)));

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    /**
     * Get default settings for a template type.
     */
    public static function getDefaultSettings($type = 'modern')
    {
        $baseSettings = [
            // School Information Display
            'show_school_logo' => true,
            'show_school_name' => true,
            'show_school_address' => true,
            'show_contact_info' => true,
            'show_principal_name' => true,
            'show_principal_signature' => false,

            // Logo & Positioning
            'logo_position' => 'left',
            'logo_size' => 'medium',

            // School Name Styling
            'school_name_size' => '24',
            'school_name_bold' => true,

            // Marks Display
            'show_theory_practical' => true,
            'show_assessment_marks' => true,
            'show_remarks' => true,
            'show_grade_points' => true,
            'show_attendance' => false,
            'show_rank' => false,

            // Smart Table Configuration
            'show_marks_in_headers' => true,
            'auto_hide_empty_columns' => true,
            'table_style' => 'bordered',
            'highlight_totals' => true,

            // Design Settings
            'header_color' => '#2563eb',
            'text_color' => '#1f2937',
            'border_style' => 'solid',
            'font_family' => 'Arial',
            'font_size' => 12,
        ];

        return match($type) {
            'modern' => array_merge($baseSettings, [
                'header_color' => '#2563eb',
                'border_style' => 'solid',
                'font_family' => 'Arial',
                'school_name_size' => '24',
                'logo_size' => 'medium',
            ]),
            'classic' => array_merge($baseSettings, [
                'header_color' => '#1f2937',
                'border_style' => 'solid',
                'font_family' => 'Times',
                'school_name_size' => '30',
                'logo_size' => 'large',
                'show_contact_info' => false,
            ]),
            'minimal' => array_merge($baseSettings, [
                'header_color' => '#6b7280',
                'border_style' => 'none',
                'font_family' => 'Helvetica',
                'school_name_size' => '18',
                'logo_size' => 'small',
                'show_theory_practical' => false,
                'show_assessment_marks' => false,
                'show_contact_info' => false,
                'show_principal_name' => false,
                'show_attendance' => false,
                'show_rank' => false,
            ]),
            default => $baseSettings
        };
    }

    /**
     * Get the CSS styles for this template.
     */
    public function getCssStyles()
    {
        $settings = $this->settings ?? [];

        // Get logo size in pixels
        $logoSize = match($settings['logo_size'] ?? 'medium') {
            'small' => '60px',
            'medium' => '80px',
            'large' => '100px',
            default => '80px'
        };

        // Get school name font weight
        $schoolNameWeight = ($settings['school_name_bold'] ?? true) ? 'bold' : 'normal';
        $schoolNameSize = $settings['school_name_size'] ?? '24';
        $fontFamily = $settings['font_family'] ?? 'Arial';
        $fontSize = $settings['font_size'] ?? '12';
        $textColor = $settings['text_color'] ?? '#1f2937';
        $headerColor = $settings['header_color'] ?? '#2563eb';
        $borderStyle = $settings['border_style'] ?? 'solid';

        $css = "
        .marksheet-container {
            font-family: {$fontFamily}, sans-serif;
            font-size: {$fontSize}px;
            color: {$textColor};
            line-height: 1.4;
        }

        .marksheet-header {
            background-color: {$headerColor};
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        .school-header-content {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            align-items: center;
            gap: 20px;
            width: 100%;
        }

        .school-logo {
            max-height: {$logoSize};
            max-width: {$logoSize};
            object-fit: contain;
            justify-self: start;
        }

        .school-seal {
            max-height: {$logoSize};
            max-width: {$logoSize};
            object-fit: contain;
            justify-self: end;
            opacity: 0.7;
        }

        .school-info h1 {
            margin: 0;
            font-size: {$schoolNameSize}px;
            font-weight: {$schoolNameWeight};
        }

        .school-info p {
            margin: 5px 0;
            font-size: " . ($fontSize + 2) . "px;
        }

        .school-info .contact-info {
            font-size: {$fontSize}px;
            margin: 0;
        }

        .marksheet-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        " . $this->getTableStyleCSS() . "

        .marksheet-table th {
            background-color: {$headerColor};
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 10px 8px;
        }

        .marksheet-table td {
            padding: 8px;
        }

        .marksheet-table .subject-cell {
            font-weight: bold;
            text-align: left;
        }

        .marksheet-table .marks-cell {
            text-align: center;
        }

        .marksheet-table .total-cell {
            font-weight: bold;
            text-align: center;
            background-color: #f8f9fa;
        }

        .marksheet-table .grade-cell {
            font-weight: bold;
            text-align: center;
            font-size: " . ($fontSize + 1) . "px;
        }

        .marksheet-table .result-pass {
            color: #10b981;
            font-weight: bold;
        }

        .marksheet-table .result-fail {
            color: #ef4444;
            font-weight: bold;
        }

        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .grade-summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
            margin-top: 40px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            padding-top: 5px;
        }

        .principal-signature {
            max-height: 50px;
            max-width: 150px;
            margin-bottom: 10px;
        }

        @media print {
            .marksheet-container {
                margin: 0;
                padding: 20px;
            }

            .no-print {
                display: none !important;
            }

            * {
                color: #000 !important;
            }
        }
        ";

        // Add custom CSS if provided
        if (!empty($this->custom_css)) {
            $css .= "\n\n/* Custom CSS */\n" . $this->custom_css;
        }

        return $css;
    }

    /**
     * Check if a setting is enabled.
     */
    public function hasSetting($key)
    {
        return $this->settings[$key] ?? false;
    }

    /**
     * Get a specific setting value.
     */
    public function getSetting($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Get the template preview data.
     */
    public function getPreviewData()
    {
        return [
            'template' => $this,
            'settings' => $this->settings,
            'css' => $this->getCssStyles(),
            'type_label' => $this->getTypeLabel(),
        ];
    }
}
