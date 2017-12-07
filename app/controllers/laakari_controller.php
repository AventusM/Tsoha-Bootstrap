<?php

class Laakaricontroller extends BaseController {
    /*
     * Copy-pasten eliminointia
     */

    public static function getCurrentDoctorID() {
        return $_SESSION['laakari'];
    }

    /*
     * Lääkärin etusivun toteuttava funktio
     * Funktio listaa lääkärin hyväksymät hoitopyynnöt sekä kaikki hyväksymättömät
     * lisäksi näytetään lääkärin omat raportit suoritetuista kotikäynneistä
     */

    public static function index() {
        $vapaatHoitopyynnot = Hoitopyynto::findAllFreeForAllDoctors();
        $laakari = Laakari::find(self::getCurrentDoctorID());
        $hyvaksytytHoitopyynnot = Hoitopyynto::findAllAcceptedForDoctor(self::getCurrentDoctorID());
        View::make('laakari/index.html', array('pyynnot' => $vapaatHoitopyynnot, 'hyvaksytytHoitopyynnot' => $hyvaksytytHoitopyynnot, 'etunimi' => $laakari->etunimi, 'sukunimi' => $laakari->sukunimi));
    }

    /*
     * Funktio suoritetaan, kun lääkäri painaa 'suorita' nappia kaikille lääkäreille vapaissa hoitopyynnöissä
     * Hyväksymisen yhteydessä pyyntöön tulee myös tehdä hoito-ohjeistusta
     */

    public static function acceptRequest() {
        $params = $_POST;
        $hyvHP = Hoitopyynto::find($params['pyynto_id']);
        $hyvHP->laakari_id = self::getCurrentDoctorID();
        $hyvHP->assignDoctor();


        Redirect::to("/laakari");
    }

    /*
     * Lääkärille näytettävä sivu, kun hän haluaa luoda hoito-ohjeen jo olemassaolevasta
     * hoitopyynnöstä, jonka hän itse on hyväksynyt
     */

    public static function createInstructions($id) {
        $muokattavaHoitopyynto = Hoitopyynto::find($id);
        View::make('laakari/editinstructions.html', array('pyynto' => $muokattavaHoitopyynto));
    }

    /*
     * createInstructions - metodin POST - osa
     */

    public static function updateInstructions() {
        $params = $_POST;
        $hoitopyynto = Hoitopyynto::find($params['pyynto_id']);
        $hoitopyynto->ohje = $params['ohje'];
        $hoitopyynto->updateInstruction();
        Redirect::to('/laakari');
    }

    /*
     * Funktio päivittää Hoitopyynto-taulua lisäämällä tekstiä Raportti-kenttään
     * Niille hoitopyynnöille, jotka lääkäri on hyväksynyt
     */

    public static function reviewReport($id) {
        if (Hoitopyynto::find($id) != null) {
            $hoitopyynto = Hoitopyynto::find($id);
            $idMatch = Hoitopyynto::indexBoundsCheck(self::getCurrentDoctorID(), $hoitopyynto->laakari_id);
            if ($idMatch) {
                View::make('laakari/editOrder.html', array('pyynto' => $hoitopyynto));
            }
        } else {
            //Paluu indeksiin toistaiseksi ainoa varma tie pois ikuisista rekursiopinoista...
            self::index();
        }
    }

    /*
     * Muista lisätä KAIKKIIN numeroparametrilinkkeihin !!! INDEXOUTOFBOUNDS CHECK !!!
     */

    public static function previewReport($id) {
        $vapaapyynto = Hoitopyynto::find($id);
        View::make('laakari/readOrder.html', array('pyynto' => $vapaapyynto));
    }

    /*
     * reviewReport - metodin POST - osa
     */

    public static function updateReport() {
        $params = $_POST;
        $hoitopyynto = Hoitopyynto::find($params['pyynto_id']);
        $hoitopyynto->raportti = $params['raportti'];
        $hoitopyynto->updateReport();
        Redirect::to('/laakari');
    }

}
