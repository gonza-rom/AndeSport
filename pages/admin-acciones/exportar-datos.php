<?php
require_once __DIR__ . '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Función para convertir número de columna a letra (A, B, C, ..., AA, AB...)
function colLetra($index)
{
    return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
}

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "andesport", 3307);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Array asociativo con campos disponibles y sus etiquetas
$camposDisponibles = [
    'id_usuario'     => 'ID Usuario',
    'usuario_nombre' => 'Nombre Usuario',
    'email'          => 'Email',
    'foto_usuario'   => 'Foto Usuario',
    'persona_nombre' => 'Nombre/s',
    'apellido'       => 'Apellido/s',
    'dni'            => 'DNI',
    'fecha_nacimiento' => 'Fecha de Nacimiento',
    'genero'         => 'Género',
    'provincia'      => 'Provincia',
    'departamento'   => 'Departamento',
    'localidad'      => 'Localidad',
    'calle'          => 'Calle',
    'altura'         => 'Altura',
    'producto_nombre' => 'Nombre de Producto',
    'descripcion'     => 'Descripcion',
    'precio'          => 'Precio',
    'stock'           => 'Stock Disponible',
    'categoria_nombre'=> 'Categoria del producto',
];

// Obtener campos seleccionados desde GET (arreglo)
$camposSeleccionados = $_GET['campos'] ?? [];

// Validar que solo existan campos válidos
$camposSeleccionados = array_filter($camposSeleccionados, function ($campo) use ($camposDisponibles) {
    return isset($camposDisponibles[$campo]);
});

// Si no seleccionaron nada, enviamos todos por defecto
if (empty($camposSeleccionados)) {
    $camposSeleccionados = array_keys($camposDisponibles);
}

// Obtener formato, default pdf
$formato = $_GET['formato'] ?? 'pdf';

// Consulta SQL para traer todos los datos que podríamos necesitar
$sql = "SELECT u.id_usuario, 
               u.nombre AS usuario_nombre, 
               u.email, 
               u.foto_usuario,

               p.nombre AS persona_nombre,  
               p.apellido, 
               p.dni, 
               p.fecha_nacimiento,
               p.genero,
               p.provincia,
               p.departamento,
               p.localidad,
               p.calle,
               p.altura
               
            FROM usuario u
            LEFT JOIN persona p ON u.id_persona = p.id_persona";

$sql_productos = "SELECT 
    p.id_producto,
    p.nombre AS producto_nombre,
    p.descripcion,
    p.precio,
    p.stock,
    c.nombre_categoria AS categoria_nombre
FROM producto p
LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
";

$result = $conexion->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conexion->error);
}

$fechaActual = date('d/m/Y H:i');

// =================== PDF ===================
if ($formato === 'pdf') {
    class PDF extends TCPDF
    {
        public function Header()
        {
            global $fechaActual;

            // Logo 
            $imageFile = $_SERVER['DOCUMENT_ROOT'] . '/AndeSport/img/Logo.jpg';
            if (file_exists($imageFile)) {
                $this->Image($imageFile, 15, 10, 25);
            }

            // Posicionar titulo a la derecha del logo
            $this->SetXY(45, 12);

            // Título
            $this->SetFont('helvetica', 'B', 16);
            $this->SetTextColor(0, 51, 102);
            $this->Cell(0, 15, 'Reporte de Datos Seleccionados', 0, 1, 'C');

            // Fecha
            $this->SetX(45); // mantiene alineacion izquierda con el titulo
            $this->SetFont('helvetica', '', 10);
            $this->SetTextColor(100, 100, 100);
            $this->Cell(0, 10, $fechaActual, 0, 1, 'C');

            // Linea separadora
            $this->SetDrawColor(0, 51, 102);
            $this->SetLineWidth(0.5);
            $this->Line(15, $this->GetY(), $this->getPageWidth() - 15, $this->GetY());
            $this->Ln(5);
        }

        public function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $this->SetTextColor(150, 150, 150);
            $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('AndeSport');
    $pdf->SetTitle('Listado de Usuarios y Personas');
    $pdf->SetMargins(20, 50, 20);
    $pdf->setAutoPageBreak(true, 20);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 11);

    $camposPorPagina = 3;
    $bloques = array_chunk($camposSeleccionados, $camposPorPagina);
    $fill = false;

    foreach ($bloques as $camposBloque) {
        // Página nueva si no es la primera
        if ($pdf->PageNo() > 1) {
            $pdf->AddPage();
        }

        $numCols = count($camposBloque);
        $anchoCol = floor(($pdf->getPageWidth() - 40) / $numCols);
        $altoFila = 10;

        // Encabezados
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->setTextColor(0);
        $pdf->setDrawColor(180, 180, 180);
        foreach ($camposBloque as $campo) {
            $pdf->Cell($anchoCol, $altoFila, $camposDisponibles[$campo], 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Datos
        $pdf->SetFont('helvetica', '', 11);
        $result->data_seek(0); // reiniciar puntero

        while ($row = $result->fetch_assoc()) {
            $pdf->setDrawColor(220, 220, 220);
            foreach ($camposBloque as $campo) {
                $pdf->SetFillColor($fill ? 245 : 255);

                if ($campo === 'foto_usuario') {
                    $rutaImagen = $row['foto_usuario'] ?? '';
                    $rutaServidor = $_SERVER['DOCUMENT_ROOT'] . '/AndeSport/' . $rutaImagen;

                    if ($rutaImagen && file_exists($rutaServidor)) {
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $altoImg = 50;
                        $anchoImg = 50;

                        $pdf->Cell($anchoImg, $altoImg, '', 1, 0, 'C', true);
                        $pdf->Image($rutaServidor, $x + 1, $y + 1, $anchoImg - 2, $altoImg - 2, '', '', '', false, 300, '', false, false, 0);
                    } else {
                        $pdf->Cell($anchoCol, $altoFila, 'Sin foto', 1, 0, 'C', true);
                    }
                } else {
                    $valor = $row[$campo] ?? '-';
                    $pdf->Cell($anchoCol, $altoFila, $valor, 1, 0, 'L', true);
                }
            }
            $pdf->Ln();
            $fill = !$fill;
        }
    }

    $pdf->Output('DatosExportados.pdf', 'I');
}

// =================== XLS, XLSX, CSV ===================
elseif (in_array($formato, ['xls', 'xlsx', 'csv'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezado con título y fecha
    $sheet->setCellValue('A1', 'Reporte de Usuarios - ' . $fechaActual);
    // Merges dinámino según cantidad de columnas seleccionadas
    $ultimaLetra = colLetra(count($camposSeleccionados));
    $sheet->mergeCells("A1:{$ultimaLetra}1");
    $sheet->getStyle('A1')->getFont()->setBold(true);

    // Títulos columnas
    $rowNum = 2;
    $colIndex = 1;
    foreach ($camposSeleccionados as $campo) {
        $sheet->setCellValue(colLetra($colIndex++) . $rowNum, $camposDisponibles[$campo]);
    }
    $rowNum++;

    // Datos
    while ($row = $result->fetch_assoc()) {
        $colIndex = 1;
        foreach ($camposSeleccionados as $campo) {
            $valor = $row[$campo] ?? '-';
            $sheet->setCellValue(colLetra($colIndex++) . $rowNum, $valor);
        }
        $rowNum++;
    }

    // Nombre archivo
    $filename = 'usuarios-' . date('Ymd-His');

    // Headers según formato y envío
    if ($formato === 'xlsx') {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename.xlsx\"");
        $writer = new Xlsx($spreadsheet);
    } elseif ($formato === 'xls') {
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename.xls\"");
        $writer = new Xls($spreadsheet);
    } else { // CSV
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"$filename.csv\"");
        $writer = new Csv($spreadsheet);
        $writer->setDelimiter(';');
    }

    $writer->save('php://output');
}

$conexion->close();
exit;
