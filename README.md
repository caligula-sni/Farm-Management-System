# WFP
 WebProject

HOW TO INITIALIZED A GITHUB REPO FROM A LOCAL FOLDER/DIRECTORY 
i love ciana . 
1. Create a public or private repository in github
   - public or private
   - no ReadMe
   - Same name as the Folder you want to push

2. Go to Terminal (Linux) or Git Bash (Windows, disregard sudo)  
3. sudo git init
4. sudo git status (check if git init have worked)
5. sudo git add . (. means ALL FILES )
6. sudo git status (check status if all files are added)
7. sudo git remote add origin https://github.com/username/repo_name
8. sudo git commit -m "Initial Commit"
9. sudo git push -u origin main (or master)
10.sudo git status (check again)


HOW TO PUSH AND PULL TO AND FROM THE REPO


Push

1. cd /path/to/your/repo
2. sudo git status
3. sudo git add .
4. sudo git commit -m "Your commit message here"
5. git push origin master

Pull

1. cd /path/to/your/repo
2. git status
3. git pull origin master

HOW TO CLONE A REPO TO A DIRECTORY

1. cd path/to/your/directory

2.git clone https://github.com/username/repository.git

3.cd repository

this comment is just for testing out SFTP



