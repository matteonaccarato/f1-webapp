# 🛜 F1-WEBAPP
**NB**: **PERSONAL** purposes only

### What you can do
🛍️ Virtually buy products in the **STORE** <br>
📰 Keep yourself updated with the most recent **NEWS** <br>
🧑‍💼 If you are an **ADMIN**, you can manage the store and users data, and email the users who subscribed to the newsletter <br>

The news, teams and drivers lists are fetched through **web-scraping**
<hr>

### HOW
🧑‍💻 Back-end ➡️ PHP (and PHPMailer) / AWS-S3 / MySQL
<br>
🧑‍💻 Front-end ➡️ JS / CSS / Bootstrap / HTML

<hr>

### USAGE
*keys.ini* file with setup keys inside is required to work properly with the DB (MySQL) and the [AWS-S3](https://aws.amazon.com/it/s3/) storage

### `git clone --recurse-submodules https://github.com/matteonaccarato/f1-webapp.git`
Clones repository and PHPMailer submodule

### `git pull --recurse-submodules`
Updates submodules

### `composer install`
Install useful dependencies


### SOURCES
☀️ [**OpenWeatherMap**](https://openweathermap.org/api) <br>
🏎️ [**Formula 1**](https://www.formula1.com/) <br>

<hr>

#### Home
![Home page](./assets/images/readme/home.png)

![Home page](./assets/images/readme/home1.png)
<hr>

#### Circuits
![Circuits page](./assets/images/readme/circuits_page.png)
<hr>

#### Store
![Store page](./assets/images/readme/store_user.png)
<hr>

#### Admin dashboard
![Admin page](./assets/images/readme/store_admin.png)
