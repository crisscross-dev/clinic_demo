if no gitignore file exists, create one with the following content:

notepad .gitignore

/vendor
/node_modules
.env
.env.*
/storage/*.key
/storage/logs
/storage/framework
/public/storage
.phpunit.result.cache


configure your git user name and email:
git config --global user.name "Your Full Name"
git config --global user.email "your_github_email@example.com"

verify
git config --global --list