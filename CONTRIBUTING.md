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
- If your code meets our style guidelines, and passes our tests, we'll consider merging it in.

## Style Guidelines

We use these guidelines to ensure our code is as uniform and easy-to-read as possible.

### Files and Directories

- Use ‘camelCase’ for directory names, or (ideally) keep them to one lower-case word.
- Use 'underscored_lower_case' for file names.
- PHP file names must end with ".php", CSS - ".css", HTML - ".html", Markdown - ".md", and JavaScript - ".js".

### Code Style

- Use two spaces to indent code.
- Always indent code contained within braces.
- Braces aren't necessary for one-line loops and statements, but indentation is.
- Don’t leave trailing white space at the end of your lines.
- Write one statement per line.
- Give your variables meaningful names.
- Use ‘camelCase’ for variable names.
- Use 'underscored_lower_case' method/function names in PHP.
- Use 'camelCase' method/function names in JavaScript.
- Try to keep logic out of HTML templates; keep frontend and backend separate.
- BlogDraw is written in Procedural-PHP, not OO-PHP.  It doesn't make use of Object-oriented functionality.  Procedural development reduces memory and processing overheads and allows BlogDraw to run where other systems couldn't.
- When writing PHP code, code blocks open with "<?php" on it's own line, and close with "?>" on it's own line.

```php
<?php
  statementOne(0);
  statementTwo();
?>
```

- an exception the the above rule can be made when you're only calling one statement.  Then your PHP code can go on one line.

```php
<p><?php text(); ?></p>
```

#### Commenting Your Code

- Add meaningful comments to clarify what your code is trying to achieve.
- Use JavaDoc-style code comments where possible for all methods/functions to document your code.
- Use inline comments wherever you feel your code may be unclear on it's own.

## Questions

If you have any questions about these guidelines, or about contributing to BlogDraw in general, feel free to [get in touch](https://tuxsoft.uk/Contact/ "Get in touch with us.").

- This was last updated for Beta2.1, BlogDraw 0.0.1.
