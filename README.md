# BileMo API

The API to connect to your client account, get the BileMo phones list and details and manage your users

## Key Features
- List our phones and get the details
- Get your information
- Manage your users

## Requirements

- PHP 7.4.3 or higher
- Composer
- Symfony 5.3
- Apache or Nginx

## Installation
Check that the Symfony requirements are met
```
 composer require symfony/requirements-checker
```

Clone the repository and enter it
```
git clone https://github.com/Tavrin/oc-project-7-api.git
cd ./oc-project-7-api
```

Create an .env.local file into the root folder and add the environment type, dev to use the fixtures
```
APP_ENV=dev
```

Configure the .env.local file to set a database url with the environment variable name being DATABASE_URL, preferably in MySQL
```
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```

Use the Makefile to automate the project installation
```
make install
```

In the .env.local file, configure the Oauth clients ID and secrets like so (replace the client IDs, secrets and the JWT passphrase by the actual values):
```
OAUTH_GITHUB_CLIENT_ID=CLIENT_ID
OAUTH_GITHUB_CLIENT_SECRET=CLIENT_SECRET
OAUTH_GOOGLE_CLIENT_ID=CLIENT_ID
OAUTH_GOOGLE_CLIENT_SECRET=CLIENT_SECRET
OAUTH_FACEBOOK_CLIENT_ID=CLIENT_ID
OAUTH_FACEBOOK_CLIENT_SECRET=CLIENT_SECRET
JWT_PASSPHRASE=PASSPRASE
```
