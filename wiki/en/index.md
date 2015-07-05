# Welcome

<!-- Feature note: {{ bd_name }} below is a custom variable. -->
<!-- Feature note: jbelelieu below is a custom replacement. -->

Welcome to {{ bd_name }} by jbelelieu. Here is a very brief quick start guide. For a more in-depth explanation,
please reference the quick start guide below:

- [More Detailed Quick Start Guide](http://www.bananadance.org/docs/?l=quick_start)


## Editing this file

<!-- Feature note: {{ currentLanguage }} below is a standard variable. -->

To edit this file, simply open "wiki/{{ currentLanguage }}/index.md" and make your desired changes.
You can use full [markdown extra](https://michelf.ca/projects/php-markdown/extra/).


## Creating More Pages

Simply create a new ".md" file in the "wiki/{{ currentLanguage }}" folder.

### Example

Perhaps you want to create documentation for the "Installation" process of your program? Simply 
create a file named "wiki/{{ currentLanguage }}/Installation.md". The program will automatically detect it are display 
it in your primary navigation.


## Creating More Categories

<!-- Feature note: "?l=sub_category_sample" below references a named route. -->

Notice how there is an "Example_Subcategory" folder in the "wiki/{{ currentLanguage }}" folder. Also notice how the 
navigation on the left shows a sub-category named "Example_Subcategory", as well as the "[Sample](?l=subcategory_example)" 
file within that directory.

That is how you create categories: you a folder anywhere in the "wiki/{{ currentLanguage }}" folder.

## Learn More

This file demonstrates a number of the "advanced" features of Banana Dance, such as:

- [Standard Variables](http://www.bananadance.org/docs/?l=standard_variables)
- [Custom Variables](http://www.bananadance.org/docs/?l=custom_variables)
- [Custom Replacements](http://www.bananadance.org/docs/?l=custom_replacements)
- [Named Routes](http://www.bananadance.org/docs/?l=named_routes)

All of them are marked with comments when you view this file's source. However, if you want to learn more about these 
features of {{ bd_name }}, please take a moment to read through the [documentation](?l=bd_docs).