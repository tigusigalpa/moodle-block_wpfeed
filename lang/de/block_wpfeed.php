<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'block_wpfeed', language 'de'
 *
 * @package   block_wpfeed
 * @copyright 2016 Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname']                                 = 'WordPress Feed';
$string['wpfeed:addinstance']                         = 'Neuen WPFeed-Block hinzufügen';
$string['wpfeed:myaddinstance']                       = 'Neuen HTML block to the My Moodle page hinzufügen';

$string['block_wpfeed_default_title']                 = 'WordPress-Nachrichten';

$string['block_wpfeed_update_cache']                  = 'WPFeed-Cache erneuern';

/*DEBUG*/
$string['block_wpfeed_debug_title']                   = 'Überwachungsinfos';
$string['block_wpfeed_request_title']                 = 'Anfragedaten';
$string['block_wpfeed_response_title']                = 'Antwortdaten';
$string['block_wpfeed_empty_response']                = 'Leere Anfrage';
$string['block_wpfeed_api_url_title']                 = 'URL-Adresse der WordPress REST API-Anfrage';
$string['block_wpfeed_settings_url_title']            = 'Zu den Einstellungen des WPFeed-Blocks übergehen';
$string['block_wpfeed_clear_cache']                   = 'Bitte nicht vergessen, wenn Sie die Blockeinstellungen erneuert haben';
$string['block_wpfeed_error_string']                  = 'Fehlerbeschreibung';

/*ERRORS DESCRIPTION*/
$string['block_wpfeed_no_posts']                      = 'Keine Angaben in der API-Antwort vorhanden';

/*SETTINGS STRINGS*/
$string['block_wpfeed_settings_title']                = 'Blockkopf';
$string['block_wpfeed_settings_hide_header']          = 'Blockkopf nicht anzeigen';
$string['block_wpfeed_settings_hide_header_desc']     = 'Wählen Sie diese Option, wenn Sie den Blockkopf auswählen';
$string['block_wpfeed_settings_wp_url']               = 'WordPress-Seite Adresse';
$string['block_wpfeed_settings_wp_url_desc']          = 'URL-Adresse Ihrer WordPress-Seite <strong>ohne Slashs</strong>. Zum Beispiel: <em>http://mysite.de</em>';
$string['block_wpfeed_api_version']                   = 'API-Version Ihrer WordPress-Seite';
$string['block_wpfeed_api_version_desc']              = 'WP API <strong>der Version 1</strong> URL des Plugin-Repositivs: <a href="https://de.wordpress.org/plugins/json-rest-api/" target="_blank">https://de.wordpress.org/plugins/json-rest-api/</a> (<em>für die Versionen WP 3.9&ndash;4.4.2</em>)<br />WP API <strong>Version 2</strong> URL des Plugin-Repositivs: <a href="https://de.wordpress.org/plugins/rest-api/" target="_blank">https://de.wordpress.org/plugins/rest-api/</a> (<em>für die Versionen WP 4.4+</em>)';
$string['block_wpfeed_settings_wp_api_prefix']        = 'Vorsatzkode WordPress REST API URI';
$string['block_wpfeed_settings_wp_api_prefix_desc']   = 'Vorsatzkode nach der URL-Adresse der Seite für die Anfragen <strong>ohne Slashs</strong>.<br />Standardmäßige Vorsatzkode für API v1: <code>{$a->default_api_prefix_v1}</code><br />Standardmäßige Vorsatzkode für API v2: <code>{$a->default_api_prefix_v2}</code><br />URL-Adresse der Anfrage an API wird im Ergebnis so aussehen <code>http://yoursite.de/{$a->default_api_prefix_v1}</code> oder <code>http://yoursite.de/{$a->default_api_prefix_v2}</code>';
$string['block_wpfeed_settings_post_type']            = 'Notwendige Art der WordPress-Eintragung';
$string['block_wpfeed_settings_post_type_desc']       = 'Notwendige Art der Eintragung aus Ihrer WordPress-Seite. Standardmäßig <code>{$a->default_post_type}</code>';
$string['block_wpfeed_settings_cache_interval']       = 'Cashing-Intervall';
$string['block_wpfeed_settings_cache_interval_desc']  = 'Cashing-Intervall der WP API Antwort in MINUTEN. Minimalwert <code>{$a->min_cache_time}</code>, 0 = ohne Cashing';
$string['block_wpfeed_settings_session_store']        = 'Ergebnisse der Api-Antwort in der Sitzung des Benutzers speichern';
$string['block_wpfeed_settings_session_store_desc']   = 'Sie können diese Variante benutzen, um die API-Antwort für den aktuellen Benutzer zu speichern. Jedes Mall, wenn der Benutzer Ihre Moodle-Seite besucht, wird die Antwort einmalig im Objekt gespeichert <code>$SESSION</code>';
$string['block_wpfeed_settings_categories']           = 'ID der Kategorien des WordPress-Eintrags';
$string['block_wpfeed_settings_categories_desc']      = 'Mehrere Kategorien sollen verteilt werden ",". <code>0 = alle Kategorien</code>';
$string['block_wpfeed_settings_thumbnail_show']       = 'Miniaturen der Einträge zeigen';
$string['block_wpfeed_settings_thumbnail_size']       = 'Miniatur-Größe';
$string['block_wpfeed_settings_thumbnail_size_desc']  = 'Die in WordPress installierten Bearbeiter der Miniatur-Größen. Werden angewandt, wenn die Option des Anzeigens der Miniaturen von WordPress eingeschaltet ist:<br /><code>thumbnail</code><br /><code>medium</code><br /><code>large</code><br /><code>small</code>';
$string['block_wpfeed_settings_thumbnail_width']      = 'Miniatur-Breite';
$string['block_wpfeed_settings_thumbnail_width_desc'] = 'HTML-Attribut der Miniatur-Breite (px), angegeben vom Verwalter. <code>0 = auto</code>';
$string['block_wpfeed_settings_thumbnail_link']       = 'Miniatur stellt einen Link dar';
$string['block_wpfeed_settings_thumbnail_link_desc']  = 'Miniatur-Abbildung zum Link auf die Eintragung machen';
$string['block_wpfeed_settings_posts_limit']          = 'Eintragungslimit';
$string['block_wpfeed_settings_posts_limit_desc']     = 'Anzahl der letzten Eintragungen, die angezeigt werden sollen';
$string['block_wpfeed_settings_post_date']            = 'Datumformat der Eintragungen';
$string['block_wpfeed_settings_post_date_desc']       = 'Anzeigeformat des Eintragungsdatums, basierend auf der PHP-Funktion <code>date</code>';
$string['block_wpfeed_settings_excerpt_length']       = 'Länge des Vorschautextes';
$string['block_wpfeed_settings_excerpt_length_desc']  = 'Länge (in Symbolen) der Textabschneidung zum Anzeigen des Vorschaus';
$string['block_wpfeed_settings_skin']                 = 'Ausgabemaske';
$string['block_wpfeed_settings_new_window']           = 'Links/Eintragungen im neuen Fenster/Register des Browsers öffnen';
$string['block_wpfeed_settings_dev_mode']             = 'Entwicklungsmodus';
$string['block_wpfeed_settings_dev_mode_desc']        = 'Ausführliche Daten für den Entwickler, funktioniert nur im nativen Fehlersuchbetrieb <code>ENTWICKLER</code>';
$string['block_wpfeed_settings_noindex']              = 'noindex anwenden';
$string['block_wpfeed_settings_noindex_desc']         = 'Tag <code>&lt;noindex&gt;</code> hinzufügen, um den Blockinhalt für die Suchsysteme wie Yandex oder Google nichtindiziert zu machen. Manchmal für SEO nützlich.';