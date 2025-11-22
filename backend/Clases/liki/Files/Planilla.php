<?php


namespace Liki\Files;
use Librerias\FPDF;


class Planilla extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // No se usará una cabecera global para este formato específico, cada sección maneja su título.
    }

    // Pie de página
    function Footer()
    {
        // No se usará un pie de página global.
    }

    // Función para añadir una sección con un título
    function AddSectionTitle($title)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode($title), 0, 1, 'L');
        $this->Ln(2);
    }

    // Función para añadir un campo con su valor
    function AddField($label, $value = '')
    {
        $this->SetFont('Arial', '', 10);
        $this->Cell(50, 7, utf8_decode($label . ':'), 0, 0, 'L');
        $this->Cell(0, 7, utf8_decode($value), 'B', 1, 'L');
        $this->Ln(2);
    }

    // Función para añadir un campo con su valor en la misma línea (para campos cortos)
    function AddInlineField($label, $value = '', $widthLabel = 40, $widthValue = 60)
    {
        $this->SetFont('Arial', '', 10);
        $this->Cell($widthLabel, 7, utf8_decode($label . ':'), 0, 0, 'L');
        $this->Cell($widthValue, 7, utf8_decode($value), 'B', 0, 'L');
    }
}





