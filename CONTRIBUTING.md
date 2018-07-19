# Contributing to BlogDraw
## Who we Are
If you're just starting out with BlogDraw development, let's first introduce you to our team.  BlogDraw is made by [@TuxSoftLimited](https://github.com/TuxSoftLimited "TuxSoft Limited"). - a small Software and Web Development company based in Lancashire, in the UK.  Our Lead Developer is [@JamesPhillipsUK](https://github.com/JamesPhillipsUK "James Phillips") - his original codebase powers BlogDraw, and all decisions about development go through him (but don't worry - he's a nice guy to work with).  A major benefit of Open-Source software (like BlogDraw) is that anyone is welcome to help out and contribute to the project; so we also welcome contributors from outside [@TuxSoftLimited](https://github.com/TuxSoftLimited "TuxSoft Limited").
## How we Talk
All converstaion about BlogDraw on our GitHub, whether it's Pull Requests, Issues, Comments, anything... must follow these simple guidelines:
 - Simple: We talk in clear English, so we can be understood by as many people as possible.
 - Professional: We talk in a professional manner - calm and collected, never rude or mean.
 - Welcoming: We talk in a way that welcomes all developers, no matter how experienced.
## How to Contribute
 - Please make changes on your own fork.
 - Test your changes out before implementing them (we'll also do this, but it's better to have multiple people testing).
 - Ensure your code meets our style guidelines.
 - Submit a Pull Request to the relevant branch.  In our case, we usually have a "dev" branch which contains all changes for the next version of BlogDraw, so it'll usually be this branch.
 - If your code meets our style guidelines, and passes our tests, we'll merge it in.
## Style Guidelines
We use these guidelines to ensure our code is as uniform and easy-to-read as possible.
### PHP
 - All code within `<?php` and `?>` should be indented.
```PHP
<?php
	//Like so.
?>
```
 - The exception for this is where there is only one line of PHP code to run.  Then it can be done inline.
```PHP
<?php //like so ?>
```
 - Don't use short tags `<?`.  We use `<?php` because it's more readable for people who are working in multiple programming languages.
 - Indent using 4-character tabs.
 - Statements and loops always use braces .
``` PHP
if (true)
{
	//Like This
}
```
 - Braces have their own line, they don't share with the function name.
```PHP
function fake_func ($FakeVar) //See!
{
	//Like this
}
```
 - Function names `are_spaced_with_underscores` and are relevant to their purpose.
```PHP
function fake_func ($FakeVar) //See!
{
...
```
 - Variable names `$AreWrittenInPascalCase` and are relevant to their purpose.
```PHP
$FakeInt = 1;
```
 - Defined constants are `NAMEDINCAPS`.
```PHP
define("NAME", "Content.");
```
 - As a minimum, we comment on the first line of each function to document what it does.
```PHP
function fake_func ($FakeVar) //fake_func is a very important function.
{
...
```
 - Large code files should have some form of explanation at the start of them so others can understand what's going on.  
```PHP
/**
 * Functions.php - this contains most of the core PHP functions that operate BlogDraw.
 * They are split up as follows:
 * - Core Content - this section runs whenever a page calls this script.  It primarily handles security and login sessions, as well as analytics.
 * - Head Output Functions - this section contains functions that return outputs which may be needed in the <head> of a template.
 * - Body Output Functions - this section contains functions that return outputs which may be needed in the <body> of a template.
 * - Engine Functions - this section contains functions that parse, operate on, and pass data to and from output functions.
**/
```
### HTML
 - Use Semantic HTML5 where possible.
```HTML
<article>This is better:</article>
<div>than this.</div>
```
 - Indent appropriately.
```HTML
<div>
	<p>inside a div</p>
</div>
```
 - Comment your markup if necessary.
```HTML
<!-- Here's the UI for the login page. -->
```
 - remember that comments get sent to the client-side.
```HTML
<!-- I shouldn't talk about my passwords, or describe the PHP that calls this. -->
<!-- I should keep my passwords to myself, and use PHP comments for the PHP-->
```
 - 99% of the time, inline styling isn't necessary.  It's not an outright ban, but question whether it's really the best way.  You can still add `<style>...</style>` tags in your HTML `<head>`.
### JavaScript
 - Make use of jQuery to make your JavaScript more managable.
 ```JS
 $('body').css('width','5rem');
 ```
 - Anonymous functions are fine, but only when wrapped in a jQuery event handler.
 
 ```JS
 $('window').resize(function ()
{
	//Do things.
});
 ```
 - Call your JavaScript at the bottom of the page, not the top.
 ```HTML
 	...
 	</body>
	<script src="#"></script>
</html>
 ```
 - try to follow the same style guidelines as you would for PHP.
 
 ## Questions
 If you have any questions about these guidelines, or about contributing to BlogDraw in general, feel free to 
[get in touch](https://tuxsoft.uk/Contact/ "Get in touch with us.").

 - This was last updated for Beta2.1, BlogDraw 0.0.1.
