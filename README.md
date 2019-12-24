<img width="25%" src="https://blogdraw.com/images/BlogDrawLogoWoT1024.png" /><img width="50%" src="https://blogdraw.com/images/BlogDrawLogoBoT1024.png" />

# BlogDraw

BlogDraw - the ultra-lightweight, super-simple, easy-to-use, fully-featured blogging platform.

## What BlogDraw is

BlogDraw is an ultra-lightweight, super-simple, easy-to-use, fully-featured blogging platform. It was initially designed by the team at TuxSoft Limited during late 2017 to give you a new blogging platform - one which is better suited to your needs than anything else on the market at the moment.

## BlogDraw Advantages

<table>
  <tr>
    <td>
      <ul>
        <li>Simplicity is Bliss.  BlogDraw is simple and easy to use with no fuss.</li>
        <li>Super Speedy!  BlogDraw is installed and ready to use in a matter of minutes.</li>
        <li>Set me up! BlogDraw is set up as soon as it's installed.  </li>
        <li>Security is Key.  There's no complex security settings to change.  It's as secure as we can make it out of the box.</li>
        <li>All the themes! You can install any BlogDraw Theme on any BlogDraw Site, and it just works. </li>
      </ul>
    </td>
    <td width="60%">
      <img src="https://blogdraw.com/images/writerb21.png" />
    </td>
  </tr>
</table>

## How to Install

<table>
  <tr>
    <td width="60%">
      <img src="https://blogdraw.com/images/accountb21.png" />
    </td>
    <td>
      <ol>
        <li>Downlad the source code, either from <a href="https://github.com/TuxSoftLimited/BlogDraw" title="BlogDraw on GitHub">Our GitHub Repository</a>, or from <a href="https://blogdraw.com" title="Visit BlogDraw">blogdraw.com</a>.</li>
        <li>Upload the zip file to your web server, and extract it.</li>
        <li>Go to your-website-url/install.php, and fill in your website and database details.</li>
        <li>Log into your-website-url/control using the details provided and click "My Account", then update your information.</li>
        <li>Start blogging!</li>
      </ol>
    </td>
  </tr>
</table>

## How to Change Templates

1. Download a new BlogDraw template from the internet and upload it to the `/template/` folder, or choose one of the default ones already in your installation.
2. Open functions.php from your web-root folder, and find the line which starts: "`define('TEMPLATE', '...');`".
3. Type the folder name of the new template in the second set of quotation marks.
4. Find the line which starts: "`define('TEMPLATEBY', '...');`", and insert the name of the template's author.  Then save the file.
5. Enjoy your new look.

## How to Update

1. Like [BlogDraw on Facebook](https://www.facebook.com/BlogDraw/ "BlogDraw's Facebook Page").
2. We'll announce when there's an update, and provide you with an updater.
3. Follow the instructions.

- If you're a developer, you can star and watch [BlogDraw-Updater on GitHub](https://github.com/TuxSoftLimited/BlogDraw-Updater "BlogDraw-Updater"), to get updates whenever we release a new update.

## System Requirements

- Apache (or similar .htaccess compatible web server).
  - with mod_rewrite installed.
- PHP 7 or above.
  - with mbstring extension.
- MYSQL.
