## About ##
WingPHP like Ruby on Rails, CakePHP, Catalyst(Perl) and more "Model View Controller" pattern.

It learning cost is very small.(but It's up to you.)

  * **Model** ........ use PDO
  * **View** ......... use Smarty
  * **Controller** ... a few class and methods

However, the amount of the code increases compared with other frameworks.
More detailed information and document is [here](http://wingphp.net/) (under construction now)


## Attention ##
It is an **ALPHA VERSION** now.
The existing probability of the bug is high, and the specification is subjected greatly.


## Sorry ##
In English, it is not good. Please teach when you find a wrong part.


---

## Sample ##
### 1. Hello World ###
**Controller**
controller/hello.php
```
class HelloController extends BaseController{
  public function view(){
    echo "HelloWorld!";
  }
}
```

access "http://yourdomain/hello/view".



### 2. Hello World (use view) ###
**Controller**
controller/hello.php
```
class HelloController extends BaseController{
  public function view(){
    $this->assign('hello', 'HelloWorld!');
    $this->display('hello/view.html');
  }
}
```

**View**
view/hello/view.html
```
<html>
<head><title>Hello!</title></head>
<body>
{$hello|escape}
</body>
</html>
```

access "http://yourdomain/hello/view".



### 3. Hello World (use model and view) ###
**Controller**
controller/hello.php
```
class HelloController extends BaseController{
  public function view(){
    $model = new HelloModel();
    $this->assign('hello', $model->hello());
    $this->display('hello/view.html');
  }
}
```

**Model**
model/hello.php
```
class HelloModel extends BaseModel{
  public function hello(){
    $buff = $this->select1('select name from hello');
    return($buff['name']);
  }
}
```

**View**
view/hello/view.html
```
<html>
<head><title>Hello!</title></head>
<body>
{$hello|escape}
</body>
</html>
```

access "http://yourdomain/hello/view".