<?php
/**
 * @param string $str
 * @return string
 */
function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * @param string $str
 * @return string
 */
function hbr(string $str): string
{
    return nl2br(h($str));
}