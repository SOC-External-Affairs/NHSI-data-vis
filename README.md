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
- **Node.js**: Version `18`
- **Composer**: Installed globally
- **NPM**: Installed globally

## Installation Steps

1. **Install PHP dependencies**  
   ```terminal
   composer install
   ```

2. **Dump autoload files**  
   ```terminal
   composer dump-autoload
   ```

3. **Install Node modules**  
   ```terminal
   npm install
   ```

4. **Configure Vite Development URL**
    - Open `vite.config.js` and update the `root` and `devServer` URL as needed
    - Update the dev server URL in `app/Hooks/Handlers/AdminMenuHandlers.php` to match your local Vite server (e.g., `http://localhost:5173`)

5. **Activate Plugin**
   - Upload to WordPress plugins directory
   - Activate through WordPress admin panel

## Usage

1. Navigate to **Excel Uploader** in WordPress admin menu
2. Upload your Excel file (.xlsx, .xls formats supported)
3. Review imported data with automatic privacy protection applied
4. Generate reports from **Reports** submenu:
   - Students by Cell Phone (Area Code)
   - International Students by Country
   - Domestic Students by State
   - Students by Zip Code
   - Students by Primary Major

## Security Features

- **Data Redaction**: Sensitive information automatically obscured
- **Column Exclusion**: High-risk columns completely filtered out
- **Secure File Deletion**: Uploaded files removed after processing
- **Access Control**: Admin-only functionality
- **Nonce Protection**: CSRF protection on all forms