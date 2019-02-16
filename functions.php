<?php

/**
 * 
 * @param type $date 
 * @return type
 */
function formatDate($date)
{
	return date('d/m/Y', strtotime($date));
}

/**
 * Formata o valor do dinheiro para reais (R$) 
 * @param float $vlprice 
 * @return type
 */
function formatPrice($vlprice)
{
	if (!$vlprice > 0) $vlprice = 0;

	return number_format($vlprice, 2, ",", ".");
}

 ?>