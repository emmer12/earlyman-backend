## Property Tweets Backend

Property tweets website repo.

## Documentation

### Authentication

1. [POST] `/api/auth/signup` - Signup
    Parameters
        - [string] firstname
        - [string] lastname
        - [string] email
        - [string] username
        - [string] password

2. [GET] `/api/auth/signup/activate/{token}` - Confirm Account

3. [GET] `/api/password/request` - Request new password
    Parameters
        - [string] email

4. [POST] `/api/password/find/{token}` - Confirm token

5. [POST]  `/api/password/reset` - Rese Password
    Parameters
        - [string] email
        - [string] password
        - [string] password_confirmation
        - [string] token

6. [POST] `api/auth/login` - Login
    Parameters
        - [string] email
        - [string] password

7. [GET] `api/auth/logout` - Logout
    Authentication Header
        - Bearer Token: {token}

### Steps to setup this repo locally

1. Clone repo by running command `git clone https://danielcoker@bitbucket.org/earlymen/backend.git`
2. cd into directory.
3. Run `composer install` command. This will install all the project dependencies, including Laravel and Passport.
4. Create a copy of .env file. `cp .env.example .env`
5. Generate app key. `php artisan key:generate`
6. Create and empty database `cipefy` and ad database information to .env file.
7. Run `php artisan migrate` to migrate database.
8. Run `php artisan serve` to start up the development server.