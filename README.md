SimpleMVC
=========

This is a very basic php-based MVC framework for MIS438

--
This project was written as a simple demonstration of how an object-oriented MVC framework works.

Most MVC-based web application frameworks are built according to this same basic principle.

##Installation
I won't go into too much detail here. Just configure the document root of your web server (or virtual host if you're using them) to point to the "public/" directory, make sure that .htaccess files are enabled (http://httpd.apache.org/docs/2.2/howto/htaccess.html), and make sure the "library/" directory is readable by the web server.


#Web Application Lifecycle
##The Framework
This section describes the general steps that occur during the execution lifecycle of a traditional MVC-based web application framework.

###1. Bootstrap
The term "bootstrap" or "bootstrapping" is very common across all kinds of software development, and usually refers to processes or resources that are automatically loaded when the program starts.

In practice, we can do this by directing every request through a single point of entry or "bootstrap file". This project uses "index.php" which is located in the /public folder. It simply loads the application and tells it to run.

index.php:
```
<?php
define('LIB_PATH', __DIR__ . '/../library');

require LIB_PATH . '/Application.php';

$application = new Application();
$application->run();
```

###2. Route
The next step in the application lifecycle is to route the request. This means figuring out what resource to load based on the request URI (Universal Resource Identifier). Because we bootstrapped the request, we can choose to interpret the URI any way we like. In this case I chose to take the first two path segments and map them to a class and a method. So, for example: "http://www.example.com/first/second" would refer to a class identified by "first", and a method inside that class referred to as "second".

*This is the first time we can really see the "C" part of the MVC pattern at work. We'll refer to the class we want to load as the "Controller" and the method as the "Action"*

library/Application.php:
```
    protected function route()
    {
        $request = $this->getRequest();

        // http://www.php.net/manual/en/function.parse-url.php
        $uri = parse_url($_SERVER['REQUEST_URI']);

        // Determine the controller and action from the path
        if (isset($uri['path'])) {
            $pathArr = explode('/', $uri['path']);

            if (isset($pathArr[1]) && ctype_alpha($pathArr[1])) {
                $request->setController($pathArr[1]);
            }

            if (isset($pathArr[2]) && ctype_alpha($pathArr[2])) {
                $request->setAction($pathArr[2]);
            }
        }
    }
```

###3. Dispatch
After determining which Controller and Action to run, it's time to load the appropriate class and call the method. This step is called "Dispatch". This method is a bit longer since I put in some error checking and added some code to pretty-up the controller and action names.

*When the action is called, its output is collected in a buffer for later use. This allows us to further modify the response before it is sent back to the browser.*
>ob\_start();  
$controller->$actionName();  
$body = ob\_get\_clean();

library/Application.php:
```
    protected function dispatch()
    {
        $request        = $this->getRequest();
        $response       = $this->getResponse();
        $controllerName = $request->getController();
        $actionName     = $request->getAction();

        // Clean up the controller name
        $controllerName = sprintf('%sController', ucfirst($controllerName));
        $controllerFile = sprintf('%s/Controllers/%s.php', __DIR__, $controllerName);

        // Create a controller instance if possible.
        if (is_readable($controllerFile)) {
            require_once $controllerFile;
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
            } else {
                throw new Exception(sprintf(
                    "The controller class (%s) could not be loaded.",
                    $controllerName
                ));
            }
        } else {
            throw new Exception(sprintf(
                "The specified controller (%s) could not be found.",
                $controllerName
            ));
        }

        // Clean up the action name
        $actionName = sprintf('%sAction', ucfirst($actionName));

        // Call the action
        if (method_exists($controller, $actionName)) {
            // Start output buffering and collect the contents
            // http://us3.php.net/manual/en/function.ob-start.php
            ob_start();
            $controller->$actionName();
            $body = ob_get_clean();
        } else {
            throw new Exception("The specified action does not exist.");
        }

        $response->setBody($body);
    }
```

###4. Render
Finally, it's time to put everything together and send the response back to the browser. For my example framework, this is the simplest of all steps. But it can get pretty involved in a full-scale framework!

*Here we one example of the "V" part of MVC. Notice that all of the program logic is completed outside of the view. Views should contain as little logic as possible (loops are about as complicated as it should get), so you can keep the html, css, javascript, etc. as clean and maintainable as possible.*

library/Application.php
```
    protected function render()
    {
        $response = $this->getResponse();
        include VIEW_DIR . '/layout.phtml';
    }
```

##The Design Pattern - MVC
In the above section, I described how the framework Bootstraps, Routes, Dispatches, and Renders. Keep in mind that this doesn't necessarily mean it's using an MVC design pattern. Routing could have been used to identify any type of resource, Dispatch could have executed any arbitrary code, and Render could have had all the html code mixed right in with the method.

But this doesn't mean that the MVC pattern is completely independent of the framework either. As a design pattern, it refers more to how the code is organized to handle certain responsibilities than to the code itself.

I won't go into too much depth on this for fear that I may be eaten alive by orthodox design-pattern zealots. But if there's one man who can speak with any authority on design patterns, it's Martin Fowler. 

This is his website: http://martinfowler.com/  
And here's a book he wrote: http://martinfowler.com/books/eaa.html  
*(I personally feel that this book should be required reading for all MIS majors)*

Nonetheless, I did include three directories in the "library/" folder named "Controllers", "Models", and "Views". The code contained within is written in an MVC pattern.

By the way, If you've ever written a UML "Class" diagram and wondered how those classes could ever be used in real code, look in the Models directory.  This is roughly how the class diagram might look for my super-simple application:


![Class Diagram](https://raw.github.com/mpinkston/SimpleMVC/master/public/images/ClassDiagram.png)