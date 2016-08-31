<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('vendor')
        ->in('samples')
        ->in('src')
        ->in('tests');

return Symfony\CS\Config\Config::create()
                ->level(Symfony\CS\FixerInterface::NONE_LEVEL)
                ->fixers([
                    // 'align_double_arrow', // Waste of time
                    // 'align_equals', // Waste of time
                    'array_element_no_space_before_comma',
                    'array_element_white_space_after_comma',
                    'blankline_after_open_tag',
                    'braces',
                    // 'concat_without_spaces', // This make it less readable
                    'concat_with_spaces',
                    'double_arrow_multiline_whitespaces',
                    'duplicate_semicolon',
                    // 'echo_to_print', // We prefer echo
                    'elseif',
                    // 'empty_return', // even if technically useless, we prefer to be explicit with our intent to return null
                    'encoding',
                    'eof_ending',
                    'ereg_to_preg',
                    'extra_empty_lines',
                    'function_call_space',
                    'function_declaration',
                    'function_typehint_space',
                    // 'header_comment', // We don't use common header in all our files
                    'include',
                    'indentation',
                    'join_function',
                    'line_after_namespace',
                    'linefeed',
                    'list_commas',
                    // 'logical_not_operators_with_spaces', // No we prefer to keep "!" without spaces
                    // 'logical_not_operators_with_successor_space', // idem
                    // 'long_array_syntax', // We opted in for the short syntax
                    'lowercase_constants',
                    'lowercase_keywords',
                    'method_argument_space',
                    'multiline_array_trailing_comma',
                    'multiline_spaces_before_semicolon',
                    'multiple_use',
                    'namespace_no_leading_whitespace',
                    'newline_after_open_tag',
                    'new_with_braces',
                    'no_blank_lines_after_class_opening',
                    // 'no_blank_lines_before_namespace', // we want 1 blank line before namespace
                    'no_empty_lines_after_phpdocs',
                    'object_operator',
                    'operators_spaces',
                    'ordered_use',
                    'parenthesis',
                    'php4_constructor',
                    'php_closing_tag',
                    'phpdoc_indent',
                    'phpdoc_inline_tag',
                    'phpdoc_no_access',
                    'phpdoc_no_empty_return',
                    'phpdoc_no_package',
                    'phpdoc_order',
                    // 'phpdoc_params', // Waste of time
                    'phpdoc_scalar',
                    // 'phpdoc_separation', // Nope, annotations are easy to read enough, no need to split them with blank lines
                    // 'phpdoc_short_description', // We usually don't generate documentation so punctuation is not important
                    'phpdoc_to_comment',
                    'phpdoc_trim',
                    'phpdoc_types',
                    'phpdoc_type_to_var',
                    // 'phpdoc_var_to_type', // This is not supported by phpDoc2 anymore
                    'phpdoc_var_without_name',
                    'php_unit_construct',
                    // 'php_unit_strict', // We sometime actually need assertEquals
                    'pre_increment',
                    'print_to_echo',
                    'psr0',
                    'remove_leading_slash_use',
                    'remove_lines_between_uses',
                    'return',
                    'self_accessor',
                    'short_array_syntax',
                    'short_bool_cast',
                    'short_echo_tag',
                    'short_tag',
                    'single_array_no_trailing_comma',
                    'single_blank_line_before_namespace',
                    'single_line_after_imports',
                    'single_quote',
                    'spaces_before_semicolon',
                    'spaces_cast',
                    'standardize_not_equal',
                    // 'strict', // No, too dangerous to change that
                    // 'strict_param', // No, too dangerous to change that
                    // 'ternary_spaces', // That would be nice, but NetBeans does not cooperate :-(
                    'trailing_spaces',
                    'trim_array_spaces',
                    'unalign_double_arrow',
                    'unalign_equals',
                    'unary_operators_spaces',
                    'unneeded_control_parentheses',
                    'unused_use',
                    'visibility',
                    'whitespacy_lines',
                ])
                ->finder($finder);
