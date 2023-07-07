<?php

/**
 * detect the preferred language of the user agent
 *
 * @copyright Roy Kaldung <roy@kaldung.com>
 * @license http://www.php.net/license/3_01.txt PHP license
 */

/**
 * split request header Accept-Language to determine the UserAgent's
 * prefered language
 *
 * @param string $defaultLanguage preselected default language
 * @return string returns the default language or a match from $availableLanguages
 */
function detectLanguage($defaultLanguage, $availableLanguages)
{
    $acceptedLanguages = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING);
    $languageList = explode(',', $acceptedLanguages);
    $chosenLanguage = $defaultLanguage;

    foreach ($languageList as $currentLanguage) {
        $currentLanguage = explode(';', $currentLanguage);
        if (preg_match('/(..)-?.*/', $currentLanguage[0], $reg)) {
            $chosenLanguage = $reg[1];
            if (in_array($chosenLanguage, $availableLanguages)) {
                break;
            }
        }
    }

    return $chosenLanguage;
}


/**
 * vim: sts=4 ts=4 sw=4 cindent fdm=marker expandtab nu
 */
