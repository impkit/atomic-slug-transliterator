<?php
/**
 * Plugin Name:       ImpKit Atomic Slug Transliterator
 * Description:       An atomic, single-purpose plugin that reliably transliterates non-Latin post, page, category, and tag slugs into standard Latin (ISO 9) across Gutenberg, Classic Editor, and REST API environments.
 * Version:           1.0.0
 * Author:            ImpKit
 * Author URI:        https: / / github.com / impkit
 * Text Domain:       atomic-slug-transliterator
 * License:           GPLv2 or later
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'impkit_get_slug_translit_table' ) ) {
    function impkit_get_slug_translit_table() {
        return array(
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 
            'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'KH', 'Ц' => 'TS', 
            'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SHCH', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 
            'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
        );
    }
}

if ( ! function_exists( 'impkit_fallback_slug_translit' ) ) {
    function impkit_fallback_slug_translit( $title, $raw_title = '', $context = 'query' ) {
        if ( $title === 'auto-draft' || $raw_title === 'auto-draft' ) {
            return $title;
        }
        if ( ( $context === 'save' || $context === 'create' ) && ! empty( $raw_title ) ) {
            $title = $raw_title;
        }
        if ( preg_match( '/[а-яёА-ЯЁ]/u', $title ) ) {
            $title = strtr( $title, impkit_get_slug_translit_table() );
        }
        return $title;
    }
    add_filter( 'sanitize_title', 'impkit_fallback_slug_translit', 1, 3 );
}

if ( ! function_exists( 'impkit_enqueue_gutenberg_slug_middleware' ) ) {
    function impkit_enqueue_gutenberg_slug_middleware() {
        if ( ! function_exists( 'get_current_screen' ) ) return;
        $screen = get_current_screen();
        if ( ! $screen || ! $screen->is_block_editor() ) return;

        $translit_json = wp_json_encode( impkit_get_slug_translit_table() );

        $js_code = "
            window.addEventListener('DOMContentLoaded', function() {
                if (typeof wp === 'undefined' || !wp.apiFetch || !wp.apiFetch.use) return;
                const iso9Table = {$translit_json};
                function transliterate(text) {
                    if (!text) return '';
                    return text.split('').map(char => iso9Table[char] !== undefined ? iso9Table[char] : char).join('')
                               .toLowerCase().replace(/[^a-z0-9\\s-]/g, '').replace(/[\\s-]+/g, '-').replace(/^-+|-+$/g, '');
                }
                wp.apiFetch.use(function(options, next) {
                    if (options.data && (options.method === 'POST' || options.method === 'PUT')) {
                        if (options.data.slug && /[а-яёА-ЯЁ]/.test(options.data.slug)) {
                            options.data.slug = transliterate(options.data.slug);
                        }
                        if (options.data.slug === undefined && options.data.title && /[а-яёА-ЯЁ]/.test(options.data.title)) {
                            options.data.slug = transliterate(options.data.title);
                        }
                        if (options.data.slug === undefined && options.data.name && /[а-яёА-ЯЁ]/.test(options.data.name)) {
                            options.data.slug = transliterate(options.data.name);
                        }
                    }
                    return next(options);
                });
            });
        ";
        wp_add_inline_script( 'wp-edit-post', $js_code );
    }
    add_action( 'enqueue_block_editor_assets', 'impkit_enqueue_gutenberg_slug_middleware' );
}

if ( ! function_exists( 'impkit_slug_plugin_row_meta' ) ) {
    function impkit_slug_plugin_row_meta( $plugin_meta, $plugin_file ) {
        if ( strpos( $plugin_file, 'atomic-slug-transliterator.php' ) !== false ) {
            $saas_url = 'https: / / impkit.dev / ?utm_source=wp_plugin&utm_medium=admin_row&utm_campaign=atomic-slug-transliterator&utm_content=v1.0.0';
            $plugin_meta[] = '<a href="' . esc_url( $saas_url ) . '" target="_blank" style="font-weight:bold;color:#722ed1;">🚀 Upgrade to ImpKit Pro</a>';
        }
        return $plugin_meta;
    }
    add_filter( 'plugin_row_meta', 'impkit_slug_plugin_row_meta', 10, 2 );
}

