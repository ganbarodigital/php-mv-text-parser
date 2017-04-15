# CHANGELOG

## develop branch

* Added support for defining grammers
  - added `GrammarRule` interface
  - added `Token` grammar
* Added support for changing how a grammar is tokenised
  - added `LexAdjuster` interface
  - added `NoopAdjuster` as the default adjuster used in our lexer
  - added `WhitespaceAdjuster` to silently consume whitespace between grammar rules
* Added some pre-defined tokens to save you having to define them in every project
  - added `T_8BIT_VALUE` lazy-match terminal token
  - added `T_AMPERSAND` lazy-match terminal token
  - added `T_ASTERISK` lazy-match terminal token
  - added `T_AT` lazy-match terminal token
  - added `T_BACKSLASH` lazy-match terminal token
  - added `T_BACKTICK` lazy-match terminal token
  - added `T_CIRCUMFLEX` lazy-match terminal token
  - added `T_CLOSE_BRACE` lazy-match terminal token
  - added `T_CLOSE_BRACKET` lazy-match terminal token
  - added `T_CLOSE_SQUARE_BRACKET` lazy-match terminal token
  - added `T_COLON` lazy-match terminal token
  - added `T_COMMA` lazy-match terminal token
  - added `T_DOLLAR` lazy-match terminal token
  - added `T_DOUBLE_QUOTE` lazy-match terminal token
  - added `T_EMPTY` meta terminal token
  - added `T_EQUALS` lazy-match terminal token
  - added `T_FORWARD_SLASH` lazy-match terminal token
  - added `T_FRACTIONAL_PERCENTAGE` meta terminal token
  - added `T_GREATER_THAN` lazy-match terminal token
  - added `T_GREATER_THAN_OR_EQUAL_TO` lazy-match terminal token
  - added `T_GREATER_THAN_OR_LESS_THAN` lazy-match terminal token
  - added `T_HASH` lazy-match terminal token
  - added `T_INT_0` strict-match terminal token
  - added `T_INT_1` strict-match terminal token
  - added `T_LESS_THAN` lazy-match terminal token
  - added `T_LESS_THAN_OR_EQUAL_TO` lazy-match terminal token
  - added `T_MINUS` lazy-match terminal token
  - added `T_OPEN_BRACE` lazy-match terminal token
  - added `T_OPEN_BRACKET` lazy-match terminal token
  - added `T_OPEN_SQUARE_BRACKET` lazy-match terminal token
  - added `T_PERCENT` lazy-match terminal token
  - added `T_PERIOD` lazy-match terminal token
  - added `T_PIPE` lazy-match terminal token
  - added `T_PLING` lazy-match terminal token
  - added `T_PLUS` lazy-match terminal token
  - added `T_QUESTION_MARK` lazy-match terminal token
  - added `T_SEMI_COLON` lazy-match terminal token
  - added `T_SINGLE_QUOTE` lazy-match terminal token
  - added `T_STERLING` lazy-match terminal token
  - added `T_TILDE` lazy-match terminal token
  - added `T_UNDERSCORE` lazy-match terminal token
* Added support for scanning input streams
  - added `ScannerPosition` value object
  - added `Scanner` interface
  - added `StreamScanner` scanner
  - added `StringScanner` scanner
