# CHANGELOG

## develop branch

* Added support for defining grammers
  - added `Grammar` interface
  - added `Token` grammar
* Added some pre-defined tokens to save you having to define them in every project
  - added `T_AMPERSAND` lazy-match token
  - added `T_ASTERISK` lazy-match token
  - added `T_AT` lazy-match token
  - added `T_BACKSLASH` lazy-match token
  - added `T_BACKTICK` lazy-match token
  - added `T_CIRCUMFLEX` lazy-match token
  - added `T_CLOSE_BRACE` lazy-match token
  - added `T_CLOSE_BRACKET` lazy-match token
  - added `T_CLOSE_SQUARE_BRACKET` lazy-match token
  - added `T_COLON` lazy-match token
  - added `T_COMMA` lazy-match token
  - added `T_DOLLAR` lazy-match token
  - added `T_DOUBLE_QUOTE` lazy-match token
  - added `T_EMPTY` meta token
  - added `T_EQUALS` lazy-match token
  - added `T_FORWARD_SLASH` lazy-match token
  - added `T_GREATER_THAN` lazy-match token
  - added `T_GREATER_THAN_OR_EQUAL_TO` lazy-match token
  - added `T_GREATER_THAN_OR_LESS_THAN` lazy-match token
  - added `T_HASH` lazy-match token
  - added `T_LESS_THAN` lazy-match token
  - added `T_LESS_THAN_OR_EQUAL_TO` lazy-match token
  - added `T_MINUS` lazy-match token
  - added `T_OPEN_BRACE` lazy-match token
  - added `T_OPEN_BRACKET` lazy-match token
  - added `T_OPEN_SQUARE_BRACKET` lazy-match token
  - added `T_PERCENT` lazy-match token
  - added `T_PERIOD` lazy-match token
  - added `T_PIPE` lazy-match token
  - added `T_PLING` lazy-match token
  - added `T_PLUS` lazy-match token
  - added `T_QUESTION_MARK` lazy-match token
  - added `T_SEMI_COLON` lazy-match token
  - added `T_SINGLE_QUOTE` lazy-match token
  - added `T_STERLING` lazy-match token
  - added `T_TILDE` lazy-match token
  - added `T_UNDERSCORE` lazy-match token
* Added support for scanning input streams
  - added `ScannerPosition` value object
