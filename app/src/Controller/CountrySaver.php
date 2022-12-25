<?php

namespace App\Controller;

use App\Entity\Country;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountrySaver extends AbstractController
{
    /* Commented to avoid involuntary country saving
    #[Route('/saver')]
    public function save(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $countries     = [
            "AF" => "Afghanistan",
            "ZA" => "Afrique du Sud",
            "AL" => "Albanie",
            "DZ" => "Algérie",
            "DE" => "Allemagne",
            "AD" => "Andorre",
            "AO" => "Angola",
            "AG" => "Antigua-et-Barbuda",
            "SA" => "Arabie saoudite",
            "AR" => "Argentine",
            "AM" => "Arménie",
            "AW" => "Aruba",
            "AU" => "Australie",
            "AT" => "Autriche",
            "AZ" => "Azerbaïdjan",
            "BS" => "Bahamas",
            "BH" => "Bahreïn",
            "BD" => "Bangladesh",
            "BB" => "Barbade",
            "BY" => "Bélarus",
            "BE" => "Belgique",
            "BZ" => "Belize",
            "BJ" => "Bénin",
            "BM" => "Bermudes",
            "BT" => "Bhoutan",
            "BO" => "Bolivie",
            "BA" => "Bosnie-Herzégovine",
            "BW" => "Botswana",
            "BR" => "Brésil",
            "BN" => "Brunei",
            "BG" => "Bulgarie",
            "BF" => "Burkina Faso",
            "BI" => "Burundi",
            "KH" => "Cambodge",
            "CM" => "Cameroun",
            "CA" => "Canada",
            "CV" => "Cap-Vert",
            "CL" => "Chili",
            "CN" => "Chine",
            "CY" => "Chypre",
            "CO" => "Colombie",
            "KM" => "Comores",
            "CG" => "Congo",
            "KP" => "Corée du Nord",
            "KR" => "Corée du Sud",
            "CR" => "Costa Rica",
            "CI" => "Côte d’Ivoire",
            "HR" => "Croatie",
            "CU" => "Cuba",
            "DK" => "Danemark",
            "DJ" => "Djibouti",
            "DM" => "Dominique",
            "EG" => "Égypte",
            "SV" => "El Salvador",
            "AE" => "Émirats arabes unis",
            "EC" => "Équateur",
            "ER" => "Érythrée",
            "ES" => "Espagne",
            "EE" => "Estonie",
            "VA" => "Vatican",
            "FM" => "Micronésie",
            "US" => "États-Unis",
            "ET" => "Éthiopie",
            "FJ" => "Fidji",
            "FI" => "Finlande",
            "FR" => "France",
            "GA" => "Gabon",
            "GM" => "Gambie",
            "GE" => "Géorgie",
            "GH" => "Ghana",
            "GR" => "Grèce",
            "GD" => "Grenade",
            "GU" => "Guam",
            "GT" => "Guatemala",
            "GN" => "Guinée",
            "GQ" => "Guinée équatoriale",
            "GW" => "Guinée-Bissau",
            "GY" => "Guyana",
            "HT" => "Haïti",
            "HN" => "Honduras",
            "HU" => "Hongrie",
            "MH" => "Îles Marshall",
            "SB" => "Îles Salomon",
            "IN" => "Inde",
            "ID" => "Indonésie",
            "IQ" => "Irak",
            "IR" => "Iran",
            "IE" => "Irlande",
            "IS" => "Islande",
            "IL" => "Israël",
            "IT" => "Italie",
            "JM" => "Jamaïque",
            "JP" => "Japon",
            "JE" => "Jersey",
            "JO" => "Jordanie",
            "KZ" => "Kazakhstan",
            "KE" => "Kenya",
            "KG" => "Kirghizistan",
            "KI" => "Kiribati",
            "KW" => "Koweït",
            "LA" => "Laos",
            "LS" => "Lesotho",
            "LV" => "Lettonie",
            "LB" => "Liban",
            "LR" => "Libéria",
            "LY" => "Libye",
            "LI" => "Liechtenstein",
            "LT" => "Lituanie",
            "LU" => "Luxembourg",
            "MK" => "Macédoine",
            "MG" => "Madagascar",
            "MY" => "Malaisie",
            "MW" => "Malawi",
            "MV" => "Maldives",
            "ML" => "Mali",
            "MT" => "Malte",
            "MA" => "Maroc",
            "MU" => "Maurice",
            "MR" => "Mauritanie",
            "MX" => "Mexique",
            "MD" => "Moldavie",
            "MC" => "Monaco",
            "MN" => "Mongolie",
            "ME" => "Monténégro",
            "MS" => "Montserrat",
            "MZ" => "Mozambique",
            "MM" => "Myanmar",
            "NA" => "Namibie",
            "NR" => "Nauru",
            "NP" => "Népal",
            "NI" => "Nicaragua",
            "NE" => "Niger",
            "NG" => "Nigéria",
            "NU" => "Niue",
            "NO" => "Norvège",
            "NZ" => "Nouvelle-Zélande",
            "OM" => "Oman",
            "UG" => "Ouganda",
            "UZ" => "Ouzbékistan",
            "PK" => "Pakistan",
            "PW" => "Palaos",
            "PA" => "Panama",
            "PG" => "Papouasie-Nouvelle-Guinée",
            "PY" => "Paraguay",
            "NL" => "Pays-Bas",
            "PE" => "Pérou",
            "PH" => "Philippines",
            "PL" => "Pologne",
            "PR" => "Porto Rico",
            "PT" => "Portugal",
            "QA" => "Qatar",
            "CF" => "République centrafricaine",
            "CD" => "République démocratique du Congo",
            "DO" => "République dominicaine",
            "CZ" => "République tchèque",
            "RO" => "Roumanie",
            "GB" => "Royaume-Uni",
            "RU" => "Russie",
            "RW" => "Rwanda",
            "KN" => "Saint-Kitts-et-Nevis",
            "SM" => "Saint-Marin",
            "VC" => "Saint-Vincent-et-les Grenadines",
            "LC" => "Sainte-Lucie",
            "WS" => "Samoa",
            "ST" => "Sao Tomé-et-Principe",
            "SN" => "Sénégal",
            "RS" => "Serbie",
            "SC" => "Seychelles",
            "SL" => "Sierra Leone",
            "SG" => "Singapour",
            "SK" => "Slovaquie",
            "SI" => "Slovénie",
            "SO" => "Somalie",
            "SD" => "Soudan",
            "SS" => "Soudan du Sud",
            "LK" => "Sri Lanka",
            "SE" => "Suède",
            "CH" => "Suisse",
            "SR" => "Suriname",
            "SZ" => "Eswatini",
            "SY" => "Syrie",
            "TJ" => "Tadjikistan",
            "TW" => "Taïwan",
            "TZ" => "Tanzanie",
            "TD" => "Tchad",
            "TH" => "Thaïlande",
            "TL" => "Timor oriental",
            "TG" => "Togo",
            "TO" => "Tonga",
            "TT" => "Trinité-et-Tobago",
            "TN" => "Tunisie",
            "TM" => "Turkménistan",
            "TR" => "Turquie",
            "TV" => "Tuvalu",
            "UA" => "Ukraine",
            "UY" => "Uruguay",
            "VU" => "Vanuatu",
            "VE" => "Venezuela",
            "VN" => "Viêt Nam",
            "YE" => "Yémen",
            "ZM" => "Zambie",
            "ZW" => "Zimbabwe"
        ];

        $number = 0;
        foreach ($countries as $code => $name){
            $cntry = new Country();
            $cntry->setName($name);
            $cntry->setFlagImgCode(strtolower($code));
            $entityManager->persist($cntry);
            $entityManager->flush();
            $number++;
        }

        return $this->render('saver.html.twig', [
            'number' => $number,
        ]);
    }*/
}