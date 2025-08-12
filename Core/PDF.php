<?php

namespace Core;

include_once 'fpdf/fpdf.php';

class PDF extends \FPDF
{
    public const PAGE_WIDTH  = 215.9;
    public const PAGE_HEIGHT = 355.6;
    public const MARGIN      = 10;
    public const LINE_HEIGHT = 5;
    private $header;

    public function __construct(Header $header, $orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->header = $header;
        $this->AliasNbPages(); // Añade esta línea
    }

    // Cabecera genérica que se aplica a cada AddPage()
    // Ajusta si quieres una cabecera distinta o nula
    protected function AddHeader(): void
    {
        $h = 30; // Altura total del encabezado
        $logoPath = 'public/images/logo-bancofassil.jpg';

        // Configurar posición inicial
        $this->SetY(self::MARGIN);

        // Primera fila: Logo | Título | Banco Fassil
        // Columna 1: Logo
        if (file_exists($logoPath)) {
            $this->Image($logoPath, self::MARGIN, $this->GetY(), 30);
        }

        // Columna 2: Título
        $this->SetX(self::MARGIN + 30);
        $this->setLetterTitle(0, 0, 0, 'B');
        $this->Cell(100, 10, $this->header->title, 0, 0, 'C');

        // Columna 3: Nombre Banco
        $this->SetX(self::PAGE_WIDTH - 60);
        $this->setLetterTitle(0, 0, 0, 'B');
        $this->Cell(50, 10, 'Banco Fassil', 0, 1, 'R');

        // Segunda fila: Logo | Subtítulo | Fecha
        $this->SetY($this->GetY() + 5);

        // Columna 1: Logo (espacio vacío)
        $this->SetX(self::MARGIN);

        // Columna 2: Subtítulo
        $this->SetX(self::MARGIN + 30);
        $this->setLetterNormal(100, 100, 100);
        $this->Cell(100, 10, $this->header->subtitle, 0, 0, 'C');

        // Columna 3: Fecha
        $this->SetX(self::PAGE_WIDTH - 60);
        $this->setLetterNormal(100, 100, 100);
        $this->Cell(50, 10, $this->header->date, 0, 1, 'R');

        // Tercera fila: Número de página
        $this->SetY($this->GetY() + 5);
        $this->SetX(self::PAGE_WIDTH - 60);
        $this->setLetterSmall(150, 150, 150);
        $this->Cell(50, 5, traducir('Página') . ' ' . $this->PageNo() . ' ' . traducir('de') . ' {nb}', 0, 0, 'R');

        // Línea separadora
        $this->SetLineWidth(0.5);
        $this->Line(self::MARGIN, $this->GetY() + 5, self::PAGE_WIDTH - self::MARGIN, $this->GetY() + 5);

        $this->SetY($h + self::MARGIN);
    }

    // Override de AddPage() para inyectar la cabecera
    public function AddPage($orientation = '', $size = '', $rotation = 0): void
    {
        parent::AddPage($orientation, $size, $rotation);
        if (isset($this->header)) {
            $this->AddHeader();
        }
    }

    // Override de Cell() para decodificar el texto
    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = ''): void
    {
        if (!isset($txt)) {
            $txt = '';
        }
        parent::Cell($w, $h, $this->getString($txt), $border, $ln, $align, $fill, $link);
    }

    // Métodos de estilo
    public function setLetterTitle(int $r = 0, int $g = 0, int $b = 0, string $style = ''): void
    {
        $this->SetTextColor($r, $g, $b);
        $this->SetFont('Arial', $style, 12);
    }
    public function setLetterNormal(int $r = 0, int $g = 0, int $b = 0, string $style = ''): void
    {
        $this->SetTextColor($r, $g, $b);
        $this->SetFont('Arial', $style, 10);
    }
    public function setLetterSmall(int $r = 0, int $g = 0, int $b = 0, string $style = ''): void
    {
        $this->SetTextColor($r, $g, $b);
        $this->SetFont('Arial', $style, 8);
    }

    public function getString(string $t): string
    {
        return utf8_decode($t);
    }

    public function getTextWidth(string $t): float
    {
        if (!isset($t)) return 0;
        return $this->GetStringWidth($t);
    }
    public function SetDash(float $black = null, float $white = null): void
    {
        if ($black !== null)
            $s = sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k);
        else
            $s = '[] 0 d';
        $this->_out($s);
    }
    function DottedCell($w, $h = 0, $txt = '', $align = '', $ln = 0, $fill = false)
    {
        // Guardamos la posición actual
        $x = $this->GetX();
        $y = $this->GetY();

        // Imprimimos la celda sin borde
        $this->Cell($w, $h, $txt, 0, $ln, $align, $fill);

        // Activamos el patrón de línea punteada
        $this->SetDash(1, 1);

        // Dibujamos una línea horizontal en la parte inferior de la celda
        $this->Line($x, $y + $h, $x + $w, $y + $h);

        // Restauramos el estilo de línea sólido
        $this->SetDash();
    }
    public function NbLines($w, $txt)
    {
        $cw = $this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
    public function getImageSizeInCm(string $path): array
    {
        if (!file_exists($path)) {
            return ['width' => 0, 'height' => 0];
        }
        $info = getimagesize($path);
        if (!$info) {
            return ['width' => 0, 'height' => 0];
        }
        $dpi = 300;
        $width = ($info[0] / $dpi) * 2.54;
        $height = ($info[1] / $dpi) * 2.54;
        return ['width' => $width, 'height' => $height];
    }
    public function setTitleStyle(bool $enabled): void
    {
        if ($enabled) {
            $this->setLetterNormal(255, 255, 255, 'B');
            $this->SetFillColor(26, 43, 74);
            $this->SetDrawColor(26, 43, 74);
        } else {
            $this->setLetterNormal();
            $this->SetFillColor(255, 255, 255);
            $this->SetDrawColor(0, 0, 0);
        }
    }
    public function setImageSectionTitle(string $title, float $currentY, float $minImageH): float
    {
        if ($currentY + $minImageH > PDF::PAGE_HEIGHT - 10) {
            $this->AddPage();
            $currentY = 50;
        }
        $this->SetXY(PDF::MARGIN, $currentY);
        $this->setTitleStyle(true);
        $this->Cell(195, 10, $title, 0, 1, 'C', true);
        return $currentY + 12;
    }
    public function placeImage(string $path, float $currentY, float $marginX, float $minH, float $availW, float $spacing, string $title): float
    {
        $dim = $this->getImageSizeInCm($path);
        $wcm = $dim['width'];
        $hcm = $dim['height'];

        if ($wcm <= 0 || $hcm <= 0) {
            return $currentY;
        }
        $wmm = $wcm * 10;
        $hmm = $hcm * 10;

        if ($wmm > $availW) {
            $scale = $availW / $wmm;
            $wmm *= $scale;
            $hmm *= $scale;
        }
        if ($hmm < $minH) {
            $scale = $minH / $hmm;
            $hmm = $minH;
            $wmm *= $scale;
            if ($wmm > $availW) {
                $scale2 = $availW / $wmm;
                $wmm *= $scale2;
                $hmm *= $scale2;
            }
        }
        if ($currentY + $hmm + $spacing > PDF::PAGE_HEIGHT - 10) {
            $this->AddPage();
            $currentY = 50;
            $currentY = $this->setImageSectionTitle($title, $currentY, $minH);
        }
        $posX = $marginX + (($availW - $wmm) / 2);
        $this->Image($path, $posX, $currentY, $wmm, $hmm);
        return $currentY + $hmm + $spacing;
    }
}
