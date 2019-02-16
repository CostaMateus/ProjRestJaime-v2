<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf5d63745d2cc68828655910dcff745f5
{
    public static $files = array (
        'bbf73f3db644d3dced353b837903e74c' => __DIR__ . '/..' . '/php-di/php-di/src/DI/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'r' => 
        array (
            'rlanches\\' => 9,
        ),
        'U' => 
        array (
            'UploadImg\\' => 10,
        ),
        'T' => 
        array (
            'TotalVoice\\' => 11,
        ),
        'P' => 
        array (
            'Psr\\Container\\' => 14,
            'PhpDocReader\\' => 13,
        ),
        'I' => 
        array (
            'Invoker\\' => 8,
            'Interop\\Container\\' => 18,
        ),
        'F' => 
        array (
            'FontLib\\' => 8,
        ),
        'D' => 
        array (
            'Dompdf\\' => 7,
            'DI\\' => 3,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'rlanches\\' => 
        array (
            0 => __DIR__ . '/..' . '/restEstevao/php-classes/src',
        ),
        'UploadImg\\' => 
        array (
            0 => __DIR__ . '/..' . '/verot/class.upload.php/src',
        ),
        'TotalVoice\\' => 
        array (
            0 => __DIR__ . '/..' . '/total-voice/php-client/src',
            1 => __DIR__ . '/..' . '/total-voice/php-client/test',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'PhpDocReader\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-di/phpdoc-reader/src/PhpDocReader',
        ),
        'Invoker\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-di/invoker/src',
        ),
        'Interop\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/container-interop/container-interop/src/Interop/Container',
        ),
        'FontLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/phenx/php-font-lib/src/FontLib',
        ),
        'Dompdf\\' => 
        array (
            0 => __DIR__ . '/..' . '/dompdf/dompdf/src',
        ),
        'DI\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-di/php-di/src/DI',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Svg\\' => 
            array (
                0 => __DIR__ . '/..' . '/phenx/php-svg-lib/src',
            ),
            'Slim' => 
            array (
                0 => __DIR__ . '/..' . '/slim/slim',
            ),
            'Sabberworm\\CSS' => 
            array (
                0 => __DIR__ . '/..' . '/sabberworm/php-css-parser/lib',
            ),
        ),
        'R' => 
        array (
            'Rain' => 
            array (
                0 => __DIR__ . '/..' . '/rain/raintpl/library',
            ),
        ),
    );

    public static $classMap = array (
        'Cpdf' => __DIR__ . '/..' . '/dompdf/dompdf/lib/Cpdf.php',
        'EasyPeasyICS' => __DIR__ . '/..' . '/phpmailer/phpmailer/extras/EasyPeasyICS.php',
        'HTML5_Data' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Data.php',
        'HTML5_InputStream' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/InputStream.php',
        'HTML5_Parser' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Parser.php',
        'HTML5_Tokenizer' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Tokenizer.php',
        'HTML5_TreeBuilder' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/TreeBuilder.php',
        'PHPMailer' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmailer.php',
        'PHPMailerOAuth' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmaileroauth.php',
        'PHPMailerOAuthGoogle' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmaileroauthgoogle.php',
        'POP3' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.pop3.php',
        'SMTP' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.smtp.php',
        'ntlm_sasl_client_class' => __DIR__ . '/..' . '/phpmailer/phpmailer/extras/ntlm_sasl_client.php',
        'phpmailerException' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmailer.php',
        'upload' => __DIR__ . '/..' . '/verot/class.upload.php/src/class.upload.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf5d63745d2cc68828655910dcff745f5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf5d63745d2cc68828655910dcff745f5::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitf5d63745d2cc68828655910dcff745f5::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitf5d63745d2cc68828655910dcff745f5::$classMap;

        }, null, ClassLoader::class);
    }
}