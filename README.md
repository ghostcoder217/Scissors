# Scissors
A PHP framework specialising in keeping PHP and HTML seperate and providing a means to maintaining links across a website from a single file.

### Concept

This program allows the developer to save snippets of pure HTML code to be constructed in PHP and echoed as a whole to the client so that HTML code is never copy-pasted between files. This method of 'constructing' HTML serverside also helps keep PHP and HTML seperated so that a developer never has to call the dreaded `<?php echo $html; ?>` all over the place, causing headaches in the long run. Plus it looks ugly, lets be honest.

Scissors also helps keep URL's consistent throughout the website by allowing the developer to store every one in a JSON file with a special identifier ex `{ "page-home" : "/", "page-about" : "about/" }`. These identifiers can then be entered into the HTML files to be replaced with it's associated URL from the JSON file.

### How it Works

Simply, Scissors allows you to create identifiers within the 'canvas' or main HTML template to be replaced with other HTML later. An example might be the simple html, head, body template.

```
<!DOCTYPE html>
<html>

  <head>
  
    {{{ head }}}
    
  </head>

  <body>
  
    {{{ body }}}
    
  </body>
  
</html>
```

In this example `{{{ head }}}` and `{{{ body }}}` are both identifiers. Let's say this template is located in `main-template.html`

Now that we have our 'canvas' we can set it with the scissors class

```
$scissors = new Scissors;
$scissors->set_canvas("main-template.html");
```

Now that we have defined the 'canvas' we can begin taking snippets of HTML and 'pasting' it to the main canvas. Let's say we have an HTML file called `meta.html` with all of our meta data ex `<title>Welcome to Scissors!</title>`. To paste it to our canvas we can simply call

```
$scissors->paste("meta.html", "head");
```

And the end result would be

```
<!DOCTYPE html>
<html>

  <head>
  
    <title>Welcome to Scissors!</title>
    
  </head>

  <body>
  
    {{{ body }}}
    
  </body>
  
</html>
```

Now let's say we have links across all of our pages which point to all of our other pages, essentially how all website navigation works. Conventionally we would define each `<a>` tag to point to a particular page 

`<a href="about.php">About</a>`

But the problem with this occurs when we want to change all links to a certain page to a new url

`<a href="info/about.php">About</a>`

Conventionally, to fix this a developer would have to go through all of the pages of the website and manually change the links. But not even this method is guaranteed to catch all occurences of the old URL.

This is where Scissor's link management system really starts to kick in. Let's say we have a part of our HTML which is dedicated to site navigation.

```
<div class="navigation">
  <a href="about.php">About</a>
  <a href="index.php">Home</a>
  <a href="terms.php">Temrms</a>
</div>
```

If we wanted to keep all links to a file we could create a json file called `main.json` with key-values as such

```
{
  "globals" : {
    "page-about" : "about.php",
    "page-home" : "index.php",
    "page-terms" : "terms.php"
  }
}
```

Then we would replace all links in our HTML with the JSON key's with an 'a:' prefix

```
<div class="navigation">
  <a href="{{{ a:page-about }}}">About</a>
  <a href="{{{ a:page-home }}}">Home</a>
  <a href="{{{ a:page-terms }}}">Temrms</a>
</div>
```

Then we could call scissors to update all of our URL's when constructing the HTML like so

```
$scissors->update_urls("main.json");
```

Now we would be left with our original HTML of

```
<div class="navigation">
  <a href="http://example.com/about.php">About</a>
  <a href="http://example.com/index.php">Home</a>
  <a href="http://example.com/terms.php">Temrms</a>
</div>
```

Now whenever we wanted to change the url for our about page all we need to do is update the value for the `page-about` key in our JSON file and all occurences of `page-about` will be updated with our new URL.

To output the final HTML a developer would simply echo the constructed HTML from the scissors class

```
echo $scissors->html;
```

