<?php namespace Pdf;

class Pdf extends \TCPDF {

   public function Header() {
        $this->SetFont('Times', 'B', 12);
        // Title
        $this->Ln(10);
     
        $this->Cell(0,5,'DIRECCIÓN NACIONAL DE MEDICAMENTOS',0,1,'C');
        $this->Cell(0,5,'UNIDAD DE INSPECCIÓN Y FISCALIZACIÓN',0,1,'C');

        // Logo
        $image_file ='img/dnm.png';
        $this->Image($image_file, 155, 10, 20, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        $image_file2 = 'img/escudo.png';
        $this->Image($image_file2, 29, 10, 20, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

       
        // Set font helveticaI courierI     
        // $image_file = K_PATH_IMAGES.'dnmlogo.png';
        //$this->Image($image_file, 180, 10, 20, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
    
     
    // Page footer
    public function Footer() {
        // Go to 1.5 cm from bottom
        $this->SetY(-17);
        $this->SetLineWidth(0.3);
        $this->Line(10,279,205,279);
        // Select Arial italic 8
        $this->SetFont('Times','I',8);
        //Direction
        $this->Cell(192,4,'Blv. Merliot y Av. Jayaque, Edif. DNM, Urb. Jardines del Volcán, Santa Tecla, La Libertad, El Salvador, América Central',0,0,'C');
        $this->ln();
        $this->Cell(192,4,'PBX: (503) 2522-5000 / Directo: (503) 2522 5029 ',0,0,'C');
        $this->SetFont('Times','I',8);
        $this->ln();
        // Print centered page number
        $this->Cell(0,5,'Página '.$this->PageNo().' de '.$this->getAliasNbPages(),0,0,'R');       
    }         
}