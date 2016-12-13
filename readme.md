## Code Styles - EditorConfig
EditorConfig helps developers define and maintain consistent coding styles
between different editors and IDEs. The EditorConfig project consists
of a file format for defining coding styles and a collection of text editor
plugins that enable editors to read the file format and adhere to defined styles.


Install EditorConfig Plugin for your Editor/IDE
http://editorconfig.org/


## SVG Icons - SVG Sprite
SVG-Sprite is automatic generated with grunt (svgstore).
Use SVG inline with SVG and USE.
SVGs have a class icon and in USE the prefix icon- and after that the filename


```HTML
<svg class="icon">
    <use xlink:href="#icon-[svg-file-name]"></use>
</svg>
```
Please delete the property fill in SVG PATH if they don't needed, so we could style the color in the css.
There are exceptions like the tvs logo, thumb- and label-icons.


Read more about inline SVG and SVG-Sprite @css-tricks: http://css-tricks.com/svg-sprites-use-better-icon-fonts/


Overview of all SVGs: http://tvspielfilm-live.lcl/svg/customTemplate-demo.html
Location and Template is defined in Gruntfile.js


## SCSS-lint
scss-lint is a tool to help keep your SCSS files clean and readable.
https://github.com/causes/scss-lint


In this project it's a grunt-task.
This task requires you to have Ruby, and scss-lint installed.
If you're on OS X or Linux you probably already have Ruby installed; test with ruby -v in your terminal.
When you've confirmed you have Ruby installed, run
```
gem update --system && gem install scss-lint
```
to install the scss-lint gem.
https://www.npmjs.com/package/grunt-scss-lint#compact

