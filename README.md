# Simple CMS
This is a developing simple content manage system with PHP.
## Features
- Basic pages creation, modification, removal, rollback
- Page templates
- User management
- URL alias
- Publish and unpublish pages
- Bulk delete/publish/unpublish pages
## Requirements
- PHP
- PHP-GD
- PHP-PDO
- Database (See [Database Requirement](#database-requirement))
- Rewrite (Apache and NGINX)
## Database Requirement
Supported databases are:
- MySQL / MariaDB
- PostgreSQL (Not tested)
- SQLite (Not tested)
- Microsoft SQL Server / SQL Azure (Not tested)

## Usage
- Page/Template Editing
  * {title} for page title
  * {body} for page content
  * {tags} for page tags
- Site Template Editing
  * {sitename} for site name
  * {content} for body content