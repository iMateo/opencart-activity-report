# OpenCart Admin Panel User Activity Monitor

This module adding new section in Admin panel which could be used for monitoring, logging and ivestigation user's activity like Login, Logout, CRUD (Create, Update, Delete) Products, Categories, Atributes etc.

## Getting Started

* Upload all files from /upload to root folder of your store
* Grant persmissions to Adminstrator group user to view/edit user/user_activity

### Compatible

Module working now on OpenCart 2.3, futher version like OpenCart 3 is under construction.
Psss... Contributors are welcome!

Module uses OpenCart Events System.

```
$this->model_extension_event->addEvent('user_activity', 'admin/model/setting/setting/editSetting/after', 'user/user_activity/addActivityEditSetting');
```

### Installing

* Upload all files from /upload to root folder of your store
* Grant persmissions to Adminstrator group user to view/edit user/user_activity
* Add a link in main menu to this module:
```
if ($this->user->hasPermission('access', 'user/user_activity')) {
				$user[] = array(
					'name'	   => 'Activity',
					'href'     => $this->url->link('user/user_activity', 'token=' . $this->session->data['token'], true),
					'children' => array()		
				);	
			}
```


### And coding style tests

Your code standards should match the [OpenCart coding standards](https://github.com/opencart/opencart/wiki/Coding-standards). We use an automated code scanner to check for most basic mistakes - if the test fails your pull request will be rejected.

Incorrect

```
if ($my_example == 1)
{

class ModelExampleExample extends Model
{

public function addExample()
{

}
else
{
```

Correct

```
if ($my_example == 1) {

class ModelExampleExample extends Model {

public function addExample() {

} else {
```

## Deployment

Currect version is early ALPHA, so please DO NOT use it on production right now. Looking for updates.

## Built With

* [OpenCart](https://opencart.com/) - Gods for all 
* [OpenCart Events System](https://github.com/opencart/opencart/wiki/Events-System) - Initial documentation about Events system in OpenCart
* [OpenCart Telegram chat (on Russian)](https://t.me/opencartforumchatru) - Feel free to join us (Russian language chanel)

## How to contribute

Fork the repository, edit and [submit a pull request](https://github.com/iMateo/opencart-activity-report/pulls).

Please be very clear on your commit messages and pull request, empty pull request messages may be rejected without reason.

Your code standards should match the [OpenCart coding standards](https://github.com/opencart/opencart/wiki/Coding-standards). We use an automated code scanner to check for most basic mistakes - if the test fails your pull request will be rejected.


## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## Authors

* **Ihor Chyshkala** - *Initial work* - [PurpleBooth](https://github.com/PurpleBooth)

Greatly looking for contributors

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* Hat tip to anyone who's code was used
* Inspiration
* etc
