
# Swoole-Mini-Framework

Unleash PHP's Power: Swoole Mini Framework – Your Gateway to Asynchronous PHP Development! ***(Not for Production Use)***


## About This Project

### Key Features

****Swoole-Powered Exploration:**** Delve into the world of Swoole, unlocking the potential of asynchronous programming, coroutines, and high-concurrency PHP applications.

****Learning By Doing:**** In the process of building this mini framework, I delved into advanced PHP concepts, honed my object-oriented programming skills, and explored much more. The hands-on experience with Swoole and real-world challenges provided invaluable insights, making this project a rich learning journey.
## Setup Instructions

### Installation

Make sure you have Composer installed. Then, run the following command to install project dependencies:

`composer install`


### Configuration

Create a `.env` file in the project root and configure your environment variables:

```
APP_ENV=local
APP_URL=http://127.0.0.1
APP_PORT=9501   
APP_NAME=Swoole

DB_DATABASE=database
DB_USER=user
DB_PASS=password
DB_HOST=host
DB_PORT=port
DB_DRIVER=pdo_mysql

```

### Run the Application

Start the Swoole server using the following command:

`php public/index.php`


Your Swoole-powered application will now be running at the specified `APP_URL` and `APP_PORT`.
