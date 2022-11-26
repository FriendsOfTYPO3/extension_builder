# EditorConfig is awesome: http://EditorConfig.org

# top-most EditorConfig file
root = true

# Unix-style newlines with a newline ending every file
[*]
charset = utf-8
end_of_line = lf
indent_style = space
indent_size = 4
insert_final_newline = true
trim_trailing_whitespace = true

# TS/JS-Files
[*.{ts,js}]
indent_size = 2

# JSON files
[*.json]
indent_style = tab

# package.json
[package.json]
indent_size = 2

# ReST files
[*.rst]
indent_size = 3
max_line_length = 80

# SQL files
[*.sql]
indent_style = tab
indent_size = 2

# TypoScript files
[*.{typoscript,tsconfig}]
indent_size = 2

# YAML files
[{*.yml,*.yaml}]
indent_size = 2

# XLF files
[*.xlf]
indent_style = tab

# .htaccess
[.htaccess]
indent_style = tab

# Markdown files
[*.md]
max_line_length = 80
