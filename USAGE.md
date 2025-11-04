# Excel Uploader Plugin

## Overview
This WordPress plugin allows administrators to upload Excel files from the admin dashboard and parse the first worksheet into a multi-dimensional PHP array keyed by the first row.

## Features
- Upload Excel files (.xlsx, .xls) from WordPress admin
- Parse first worksheet automatically
- Convert data to PHP array with first row as keys
- Display parsed data preview in admin
- Secure file handling with proper validation

## Usage

1. **Access the Plugin**
   - Go to WordPress Admin Dashboard
   - Navigate to "Excel Uploader" in the admin menu

2. **Upload Excel File**
   - Click "Choose File" and select your Excel file
   - Supported formats: .xlsx, .xls
   - Click "Upload and Parse Excel"

3. **View Results**
   - Successfully parsed data will be displayed in a table
   - First row of Excel becomes array keys
   - Remaining rows become data arrays

## Technical Details

### File Structure
```
excel-uploader/
├── app/
│   ├── Controllers/
│   │   └── ExcelUploadController.php
│   ├── Views/
│   │   └── excel-upload-form.php
│   └── App.php
├── vendor/ (PhpSpreadsheet)
└── excel-uploader.php
```

### Data Format
Input Excel:
```
Name    | Email           | Age
John    | john@email.com  | 25
Jane    | jane@email.com  | 30
```

Output PHP Array:
```php
[
    ['Name' => 'John', 'Email' => 'john@email.com', 'Age' => 25],
    ['Name' => 'Jane', 'Email' => 'jane@email.com', 'Age' => 30]
]
```

## Dependencies
- PhpOffice/PhpSpreadsheet (automatically installed via Composer)
- WordPress 5.2+
- PHP 7.2+

## Security
- Only administrators can upload files
- File type validation
- Proper nonce verification
- Temporary file handling