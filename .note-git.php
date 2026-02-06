step 1: initialize git repository
git init

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

step2: configure git user name and email

git config --global user.name "Your Full Name"
git config --global user.email "your_github_email@example.com"

step 3: verify configuration
verify
git config --global --list

step 4: rename default branch to main
git branch -M main

ste 5: add remote repository
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git

type so it wont conflict to github repository
git add .gitignore README.md

git commit -m "Resolve gitignore merge conflict"

step 6:
git push -u origin main