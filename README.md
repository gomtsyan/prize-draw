# Prize-draw README

### init

After installing the project, you need to insert the initial data using the console command.


  ```javascript
  php yii add-start-data/create
  ```

### Run Server App

For start Server API

  ```javascript
  cd serverAPI
  ```

  Run this command

  ```javascript
  npm run api_server
  ```
  
for the server to work, it is necessary to register with the same email address using the post method at

```javascript
  http://localhost:8000/api/users/register
  ```
###transmitted data
  * name
  * email
  * password
  * password_confirm

