# Contributing to BlogDraw

## Who we Are

If you're just starting out with BlogDraw development, let's first introduce you to our team.  BlogDraw is made by [@TuxSoftLimited](https://github.com/TuxSoftLimited "TuxSoft Limited"). - a small Software and Web Development company based in Lancashire, in the UK.  Our Lead Developer is [@JamesPhillipsUK](https://github.com/JamesPhillipsUK "James Phillips") - his original codebase powers BlogDraw, and decisions about development all go through him (but don't worry - he's a nice guy to work with).  A major benefit of Open-Source software (like BlogDraw) is that everyone's welcome to help out and contribute to the project; so we also welcome contributors from outside [@TuxSoftLimited](https://github.com/TuxSoftLimited "TuxSoft Limited").

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
- If your code meets our style guidelines, and passes our tests, we'll consider merging it in.

## Style Guidelines

We use these guidelines to ensure our code is as uniform and easy-to-read as possible.

### Files and Directories

- Use ‘camelCase’ for directory names, or (ideally) keep them to one lower-case word.
- Use 'underscored_lower_case' for file names.
- PHP file names must end with ".php", CSS - ".css", HTML - ".html", Markdown - ".md", and JavaScript - ".js".

### Code Style

- Use two spaces to indent code.
- Braces have their own lines.

```php
function do_this()
{
  ...
}// Correct.

function do_this(){...}// Incorrect.

function do_this(){
  ...
}// Also incorrect.
```

- Always indent code contained within braces.
- Braces aren't necessary for one-line loops and statements, but indentation is.

```php
if (1 == 1)
  return true;// Correct.

if (2 == 2)
{
  return true;// Incorrect.
}
```

- Don’t leave trailing white space at the end of your lines.
- Write one statement per line.
- if statements, loop structures, etc. use a space between their name and parameter list, function calls don't.

```php
function do_this()
{
  if (1==1)
    do_that();
}// Correct.

function do_this ()
{
  if(1==1)
    do_that ();
}// Incorrect.
```

- Give your variables meaningful names.
- Use ‘camelCase’ for variable names.

```php
thisInteger = 7;
```

- Use 'underscored_lower_case' for method/function names in PHP.

```php
function this_funtion()
{
  ...
}
```

- Use 'camelCase' for method/function names in JavaScript.

```js
function thisFuntion()
{
  ...
}
```

- Try to keep logic out of HTML templates; keep frontend and backend separate.
- BlogDraw is written in Procedural-PHP, not OO-PHP.  It doesn't make use of Object-oriented functionality.  Procedural development reduces memory and processing overheads and allows BlogDraw to run where other systems couldn't.
- When writing PHP code, use long tags, not short tags.  Short tags only work when they're enabled by server admins, and are scheduled to be deprecated in a future update to PHP.

```php
<?php
// Correct.
?>

<?
// Incorrect.
?>
```

- When writing PHP code, code blocks open with ```<?php``` on it's own line, and close with ```?>``` on it's own line.

```php
<?php
do_this(0);
do_that(9);
?>// Correct

<?php do_this(0);
do_that(9); ?>// Incorrect.
```

- An exception the the above rule can be made when you're only calling one statement.  Then your PHP code can go on one line.

```php
<p><?php text(); ?></p>
```

#### Commenting Your Code

- Add meaningful comments to clarify what your code is trying to achieve.
- Use JavaDoc-style code comments where possible for all methods/functions to document your code.

```php
/**
 * This squares a given input, then adds 5.
 * @param input - The inputted number to preform this functon on.
 * @return output - The resultant number after this function.
 **/
function square_then_add_five($input)
{
  $output = $input * $input;
  $output += 5;
  return $output;
}
```

- Use inline comments wherever you feel your code may be unclear on it's own.

## Questions

If you have any questions about these guidelines, or about contributing to BlogDraw in general, feel free to [get in touch](https://tuxsoft.uk/Contact/ "Get in touch with us.").

- This was last updated for Beta 2.1, BlogDraw 0.0.1.
