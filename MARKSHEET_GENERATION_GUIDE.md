# 📋 **Complete Marksheet Generation Guide**

## 🎯 **Step-by-Step Process**

### **Step 1: Understand the Workflow**
```
Draft → Scheduled → Ongoing → Submitted → Approved → **Published** → Locked
                                                        ↑
                                              Marksheets can be generated here
```

### **Step 2: Prepare Your Exam for Marksheet Generation**

#### **2.1 Enter Marks**
1. Go to **Mark Entry** → Select your exam
2. Enter marks for all students in all subjects
3. Save the marks (Status: `draft`)

#### **2.2 Submit Marks for Approval**
1. Go to **Mark Entry** → Click on your exam
2. Click **"Submit All Marks"** button (yellow button)
3. Mark status changes from `draft` → `submitted`

#### **2.3 Approve Marks (Admin/Teacher)**
1. Go to **Mark Entry** → Click on your exam
2. Click **"Approve All Marks"** button (green button)
3. Mark status changes from `submitted` → `approved`

#### **2.4 Publish the Exam (Required for Marksheets)**
1. Go to **Examinations** → Find your exam
2. Click **"Change Status"** → Select **"Published"**
3. Exam status changes from `approved` → `published`

## 🚨 **IMPORTANT: Mark Approval Process**

### **How to Approve Marks (The Missing Step!)**

**Problem**: Your exam is published but shows "0 approved marks"

**Solution**: Follow these exact steps:

1. **Go to Mark Entry Dashboard**
   - Navigate to **Mark Entry** → Click on your exam
   - You'll see the exam dashboard

2. **Submit Marks First**
   - If you see a **yellow "Submit All Marks"** button, click it
   - This changes mark status from `draft` → `submitted`

3. **Approve Marks**
   - After submitting, you'll see a **green "Approve All Marks"** button
   - Click it to change mark status from `submitted` → `approved`

4. **Generate Marksheets**
   - Once marks are approved, you'll see a **blue "Generate Marksheets"** button
   - Click it to go to marksheet generation

### **Step 3: Generate Marksheets**

#### **3.1 Access Marksheet Generation**
1. Go to **Reports** → **Marksheets**
2. You'll see published exams available for marksheet generation

#### **3.2 Select Exam and Generation Type**
1. Click **"Generate Marksheets"** for your published exam
2. **Choose Generation Type**:
   - **Class-wise**: Select specific class to generate marksheets for
   - **All Classes**: Generate for all students across classes
   - **Individual**: Generate single student marksheets
   - **Bulk**: Generate multiple students at once

#### **3.3 Class Selection (if exam covers multiple classes)**
1. If exam covers multiple classes, you'll see class selection cards
2. Click on a specific class card to generate for that class only
3. Or click "All Classes" to see all students

#### **3.4 Template and Student Selection**
1. Choose template (Modern, Classic, or Minimal)
2. Select students using checkboxes
3. Use "Select All" or "Deselect All" for bulk operations
4. Click **"Generate Bulk Marksheets"** for selected students

#### **3.5 Individual Generation**
1. For each student, you can:
   - **Preview**: See marksheet before generating
   - **Generate PDF**: Download individual marksheet

## 🎨 **Template Options**

### **Modern Template**
- Clean, contemporary design
- Color-coded sections
- QR verification code
- Professional layout

### **Classic Template**
- Traditional academic format
- Institutional branding
- Formal appearance
- Standard layout

### **Minimal Template**
- Simple, efficient design
- Cost-effective printing
- Basic information only
- Clean typography

## 🔧 **Troubleshooting**

### **"No Published Exams Found"**
- **Cause**: No exams have been published yet
- **Solution**: Follow Step 2.4 to publish an exam

### **"No Students with Approved Marks"**
- **Cause**: Marks haven't been approved yet
- **Solution**: Follow Steps 2.2 and 2.3

### **"Column 'status' not found"**
- **Cause**: Database query using wrong column name
- **Solution**: Already fixed in the code updates

## 📊 **Features of the New Design**

### **Enhanced UI**
- Modern Bootstrap 4 design
- Responsive layout
- Interactive elements
- Professional appearance

### **Improved Functionality**
- Bulk selection with checkboxes
- Template preview
- Real-time status updates
- Better error handling

### **User Experience**
- Clear step-by-step process
- Visual feedback
- Intuitive navigation
- Helpful tooltips

## 🚀 **Quick Start**

1. **Publish an exam**: Examinations → Change Status → Published
2. **Go to Marksheets**: Reports → Marksheets
3. **Generate**: Select exam → Choose template → Generate

## 🎯 **Generation Options Explained**

### **1. Class-wise Generation**
- **When to use**: When exam covers multiple classes but you want marksheets for specific class
- **How**: Select class card → Choose template → Select students → Generate
- **Output**: Marksheets for selected class students only

### **2. Individual Student Generation**
- **When to use**: When you need marksheet for specific student
- **How**: Click "Preview" or "PDF" button next to student name
- **Output**: Single marksheet PDF for that student

### **3. Bulk Generation**
- **When to use**: When you need marksheets for multiple students at once
- **How**: Select multiple students → Choose template → Click "Generate Bulk"
- **Output**: Single PDF containing all selected student marksheets

### **4. All Classes Generation**
- **When to use**: When exam covers multiple classes and you want all students
- **How**: Click "Generate for All" → Select students → Generate
- **Output**: Marksheets for all students across all classes

## 📝 **Notes**

- Only **published** exams appear in marksheet generation
- Only students with **approved** marks can have marksheets generated
- Templates can be switched without regenerating
- Bulk generation creates a single PDF with all selected students
- Individual generation creates separate PDFs per student
- Class selection is available only for exams covering multiple classes

## 🔧 **Troubleshooting Common Issues**

### **"No students with approved marks"**
1. Go to **Mark Entry** → Select your exam
2. Click **"Submit All Marks"** (if marks are in draft)
3. Click **"Approve All Marks"** (if marks are submitted)
4. Return to marksheet generation

### **"No published exams found"**
1. Go to **Examinations** → Find your exam
2. Click **"Change Status"** → Select **"Published"**
3. Return to marksheet generation

### **"Exam status is published but no marksheets"**
1. Check if marks are approved (not just submitted)
2. Follow the mark approval process above
3. Ensure students have marks entered for the exam

---

**Need Help?** Follow the mark approval workflow: Enter → Submit → Approve → Publish → Generate
