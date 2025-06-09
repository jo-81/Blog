<?php
// .php-cs-fixer.php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/Framework',
        __DIR__ . '/tests',
    ])
    ->exclude([
        'vendor',
        'cache',
        'storage',
        'tmp',
    ])
    ->name('*.php')
    ->notName('*.html.twig') // Si vous utilisez des templates
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        // === RÈGLES DE BASE ===
        '@PSR12' => true,
        '@PHP80Migration' => true,
        '@PhpCsFixer' => true,
        
        // === TABLEAU ET STRUCTURES ===
        'array_syntax' => ['syntax' => 'short'],
        'array_indentation' => true,
        'trim_array_spaces' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'trailing_comma_in_multiline' => [
            'elements' => ['arrays', 'arguments', 'parameters']
        ],
        
        // === ESPACES ET INDENTATION ===
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'try', 'if', 'foreach']
        ],
        'no_extra_blank_lines' => [
            'tokens' => [
                'curly_brace_block',
                'extra',
                'parenthesis_brace_block',
                'square_brace_block',
                'throw',
                'use'
            ]
        ],
        'single_blank_line_at_eof' => true,
        'line_ending' => true,
        
        // === IMPORTS ET NAMESPACES ===
        'ordered_imports' => [
            'sort_algorithm' => 'length',
            'imports_order' => ['class', 'function', 'const']
        ],
        'no_unused_imports' => true,
        'single_import_per_statement' => true,
        'global_namespace_import' => [
            'import_classes' => false,
            'import_constants' => false,
            'import_functions' => false
        ],
        
        // === CLASSES ET MÉTHODES ===
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one',
                'method' => 'one',
                'property' => 'one'
            ]
        ],
        'method_chaining_indentation' => true,
        'visibility_required' => ['elements' => ['property', 'method', 'const']],
        'single_class_element_per_statement' => true,
        
        // === FONCTIONS ===
        'function_declaration' => ['closure_function_spacing' => 'one'],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false
        ],
        'lambda_not_used_import' => true,
        
        // === OPÉRATEURS ===
        'binary_operator_spaces' => [
            'default' => 'single_space',
            'operators' => [
                '=>' => 'single_space',
                '=' => 'single_space'
            ]
        ],
        'concat_space' => ['spacing' => 'one'],
        'unary_operator_spaces' => true,
        'ternary_operator_spaces' => true,
        
        // === CHAÎNES DE CARACTÈRES ===
        'single_quote' => ['strings_containing_single_quote_chars' => false],
        'escape_implicit_backslashes' => true,
        'heredoc_to_nowdoc' => true,
        
        // === COMMENTAIRES ===
        'single_line_comment_style' => ['comment_types' => ['hash']],
        'multiline_comment_opening_closing' => true,
        'comment_to_phpdoc' => false, // Désactivé pour éviter les conversions automatiques
        
        // === PHPDOC ===
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_package' => true,
        'phpdoc_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'phpdoc_var_without_name' => true,
        
        // === CONTRÔLE DE FLUX ===
        'no_break_comment' => false,
        'no_superfluous_elseif' => true,
        'no_useless_else' => true,
        'switch_case_semicolon_to_colon' => true,
        'switch_case_space' => true,
        
        // === NETTOYAGE ===
        'no_empty_statement' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_curly_braces' => true,
        'no_whitespace_in_blank_line' => true,
        'no_trailing_whitespace' => true,
        'no_trailing_whitespace_in_comment' => true,
        
        // === RÈGLES SPÉCIFIQUES DÉSACTIVÉES ===
        'yoda_style' => false, // Permet $var == 'value' au lieu de 'value' == $var
        'increment_style' => false, // Permet ++$i et $i++
        'php_unit_test_class_requires_covers' => false, // Pas obligatoire pour les tests
        
        // === RÈGLES MODERNES PHP ===
        'declare_strict_types' => true, // À activer si vous voulez le mode strict
        'nullable_type_declaration_for_default_null_value' => true,
        'modernize_types_casting' => true,
    ])
    ->setFinder($finder);