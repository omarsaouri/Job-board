# PHP Simple Job Board

A lightweight, easy-to-use job board application built with PHP and Bootstrap, leveraging Supabase for the backend database. This project demonstrates the simplicity and effectiveness of PHP for web development (learned it in 2 days lol), created with assistance from Claude AI.

## Features

- Clean, responsive Bootstrap interface
- Job posting and browsing functionality
- User authentication via Supabase
- Easy-to-understand PHP codebase
- Mobile-friendly design
- Real-time data capabilities with Supabase

## Tech Stack

- PHP 7.4+
- Supabase (Database & Authentication)
- Bootstrap 5
- HTML5 & CSS3
- MAMP (Development Environment)

## Installation

1. Clone this repository:
```bash
git clone https://github.com/yourusername/php-simple-job-board.git
```

2. Configure Supabase connection:
- Copy `config/supabase.example.php` to `config/supabase.php`
- Update with your Supabase project URL and anon key:
```php
define('SUPABASE_URL', 'your-project-url');
define('SUPABASE_ANON_KEY', 'your-anon-key');
```

3. Server Requirements:
- PHP 7.4 or higher
- Apache/Nginx web server
- PHP cURL extension enabled

## Database Setup

1. Create a new project in Supabase
2. Use the following SQL to create your tables:
```sql
-- Example table structure (adjust according to your actual schema)
create table jobs (
  id bigint generated by default as identity primary key,
  title text not null,
  description text,
  company text,
  location text,
  created_at timestamp with time zone default timezone('utc'::text, now()) not null
);

-- Add appropriate RLS policies
```

## Development Setup

1. Install MAMP or similar local development environment
2. Point the document root to the project folder
3. Access the application through localhost
4. Ensure your PHP environment has cURL enabled for Supabase API calls

## Project Structure

```
php-simple-job-board/
├── config/
│   ├── supabase.php
│   └── config.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── public/
│   ├── css/
│   ├── js/
│   └── images/
├── services/
│   └── supabase.php
└── index.php
```

## Environment Variables
You'll need to set up the following environment variables:
```env
SUPABASE_URL=your-project-url
SUPABASE_ANON_KEY=your-anon-key
```

## Contributing

Feel free to fork this project and submit pull requests. This is an educational project meant to showcase PHP's capabilities for beginners.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Built to demonstrate PHP's learning curve
- Created with assistance from Claude AI
- Supabase for backend database and authentication
- Bootstrap for responsive design
- MAMP for local development environment

## Author

[Your Name]

## Support

For support, please open an issue in the GitHub repository.
