# Excel Uploader

A WordPress plugin for securely importing and analyzing student attendee data from Excel files with built-in privacy protection and data redaction.

## What This Plugin Does

- **Secure Excel Import**: Upload Excel files containing student attendee data
- **Privacy Protection**: Automatically redacts sensitive information (last names, full addresses, phone numbers)
- **Data Analysis**: Generate geographic and demographic reports from imported data
- **Column Filtering**: Excludes sensitive columns (SSN, financial data, medical info) during import
- **Secure File Handling**: Uploaded files are automatically deleted after processing

## Expected Excel File Format

Your Excel file should contain student attendee data with these supported columns:

### Required/Recommended Columns:
- `Division` - Academic division
- `Concentration` - Area of study
- `Year Attended` - Year of attendance
- `NetID` - Student network ID (letters will be replaced with "xxx")
- `Preferred First` - Student's preferred first name
- `Legal First` - Student's legal first name
- `Email` - Student email (username will be redacted)
- `Student Cell` - Phone number (reduced to area code only)
- `Hometown` - Student's hometown
- `Home State/Province` - State or province
- `Home Country` - Country of origin
- `Zip Code` - Postal code
- `Primary School` - Primary school attended
- `Primary Major Enrollment` - Major field of study

### Privacy-Protected Columns:
These columns will be automatically redacted or excluded:
- Last names → `[REDACTED]`
- Full addresses → `[ADDRESS REDACTED]`
- Phone numbers → Area code only
- Email addresses → `[REDACTED]@domain.com`
- NetID → Numbers kept, letters become "xxx"

### Excluded Columns:
These columns will be completely ignored during import:
- SSN, Social Security Number
- Full Address, Street Address
- Personal/Home Phone numbers
- Financial Aid, Tuition, Scholarship data
- Medical, Health, Disability information

## System Requirements

- **WordPress**: 5.0+
- **PHP**: 7.4+
- **Composer**: Installed globally

## Installation Steps

1. **Install PHP dependencies**  
   ```terminal
   composer install
   ```

2. **Dump autoload files**  
   ```terminal
   composer dump-autoload
   ```

3. **Activate Plugin**
   - Upload to WordPress plugins directory
   - Activate through WordPress admin panel

## Usage

1. Navigate to **Excel Uploader** in WordPress admin menu
2. Upload your Excel file (.xlsx, .xls formats supported)
3. Review imported data with automatic privacy protection applied
4. **Search Records**: Use search fields to filter by:
   - First Name (searches both preferred and legal first names)
   - NetID Numbers (numerical values only)
5. **Manage Records**:
   - Sort by any column (click column headers)
   - Delete individual records (red Delete button with confirmation)
   - Delete all records (bulk delete with confirmation)
6. Generate reports from **Reports** submenu:
   - Students by Cell Phone (Area Code)
   - International Students by Country
   - Domestic Students by State
   - Students by Zip Code
   - Students by Primary Major
   - State Trends by Year (interactive line chart)
   - Country Trends by Year (interactive line chart)
7. Check for duplicate records using **Check Duplicates** menu

## Security Features

- **Data Redaction**: Sensitive information automatically obscured
- **Column Exclusion**: High-risk columns completely filtered out
- **Secure File Deletion**: Uploaded files removed after processing
- **Access Control**: Admin-only functionality
- **CSRF Protection**: Unique nonces for all forms and delete actions
- **Input Validation**: Search parameters sanitized and length-limited
- **SQL Injection Prevention**: Parameterized queries for all database operations
- **XSS Protection**: Output escaping in templates