<?php

// Using *passthru* or other *exec* functions may lead to php7.0-fpm
// start creating a lot of child processes:
//
//     * https://bugs.php.net/bug.php?id=70185
//     * https://bugs.php.net/bug.php?id=73056
//     * https://bugs.php.net/bug.php?id=73342
//
// Using *proc_open* seems to have not this problem. Although it has
// an issue with exit status. The fix was taken from the following url:
// http://php.net/manual/en/function.proc-close.php#56798
function shell(array $args, $cwd = null, $env = null, $stdin = null)
{
    $desc = [
        0 => ['pipe', 'r'], // stdin
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w'], // stderr
        3 => ['pipe', 'w'], // status code
    ];

    $cmd = implode(' ', [$args[0], ...array_map(fn ($s) => shell_arg($s), array_slice($args, 1))]);

    $fp = proc_open("$cmd; echo $? >&3", $desc, $pipes, $cwd, $env);
    if (isset($stdin)) {
        fwrite($pipes[0], $stdin);
    }
    fclose($pipes[0]);

    $stdout = '';
    while (!feof($pipes[1])) {
        $stdout .= fread($pipes[1], 10*1024);
    }
    fclose($pipes[1]);

    $stderr = '';
    while (!feof($pipes[2])) {
        $stderr .= fread($pipes[2], 10*1024);
    }
    fclose($pipes[2]);

    $status = trim(fgets($pipes[3]));
    fclose($pipes[3]);

    proc_close($fp);

    if ($status != 0) {
        throw new Exception("Command execution failed: $status\n" . print_r(compact('cmd', 'status', 'stderr', 'stdout'), true));
    }

    return $stdout;
}

// escapeshellarg strips out unicode symbols:
//
//    echo escapeshellarg('<div>Ene Lösugtaprfk,wSbm/');
//    '<div>Ene Lsugtaprfk,wSbm/'
//
// http://markushedlund.com/dev/php-escapeshellarg-with-unicodeutf-8-support
function shell_arg($arg)
{
    return "'" . str_replace("'", "'\\''", $arg) . "'";
}
