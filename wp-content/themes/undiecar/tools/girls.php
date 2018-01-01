<?php

if ( ! isset( $_GET['girls'] ) ) {
	return;
}

set_time_limit ( 900 );
ini_set('max_execution_time', 0); 


/*
$directory = get_template_directory() . '/tools/baby-names/';
$txt_files = scandir( $directory, 1 );

foreach ( $txt_files as $key => $txt_file_name ) {

	if ( '.TXT' !== substr( $txt_file_name, -4 ) ) {
		continue;
	}

	// Get TXT data
	$txt_file_path = $directory . $txt_file_name;
	$txt_file_content = file_get_contents( $txt_file_path );
	//$csv_file_content = str_replace( '"', '', $csv_file_content );
	$txt_file_rows = explode( "\n", $txt_file_content );
	foreach ( $txt_file_rows as $key2 => $txt_file_row ) {
		$exploded_row = explode( ',', $txt_file_row );
		if ( isset( $exploded_row[1] ) && 'F' === $exploded_row[1] ) {
			$girl_names_array[] = $exploded_row[3];
		} else if ( isset( $exploded_row[1] ) && 'M' === $exploded_row[1] ) {
			$boy_names_array[] = $exploded_row[3];
		}
	}

	$girl_names_array = array_unique( $girl_names_array );
	$boy_names_array = array_unique( $boy_names_array );
}
//print_r( $boy_names_array );die;
$girl_names_array = array_diff( $girl_names_array, $boy_names_array );
print_r( $girl_names_array );die;
$girl_names = '';
foreach( $girl_names_array as $key => $girl_name ) {
	$girl_names .= $girl_name . ',';
}

$girl_names .= 'Tamara,Ronda,Lauralee,Emma,Olivia,Sophia,Ava,Isabella,Mia,Abigail,Emily,Charlotte,Harper,Madison,Amelia,Elizabeth,Sofia,Evelyn,Avery,Chloe,Ella,Grace,Victoria,Aubrey,Scarlett,Zoey,Addison,Lily,Lillian,Natalie,Hannah,Aria,Layla,Brooklyn,Alexa,Zoe,Penelope,Leah,Audrey,Savannah,Allison,Samantha,Nora,Skylar,Camila,Anna,Paisley,Ariana,Ellie,Aaliyah,Claire,Violet,Stella,Sadie,Mila,Gabriella,Lucy,Arianna,Kennedy,Sarah,Madelyn,Eleanor,Kaylee,Caroline,Hazel,Hailey,Genesis,Kylie,Autumn,Piper,Maya,Nevaeh,Serenity,Peyton,Mackenzie,Bella,Eva,Naomi,Aubree,Aurora,Melanie,Lydia,Brianna,Ruby,Katherine,fCassidyAlice,Cora,Julia,Madeline,Faith,Annabelle,Alyssa,Isabelle,Vivian,Gianna,Quinn,Clara,Khloe,Alexandra,Eliana,Sophie,London,Elena,Kimberly,Maria,Luna,Willow,Jasmine,Kinsley,Kayla,Delilah,Andrea,Natalia,Lauren,Rylee,Adalynn,Mary,Ximena,Jade,Liliana,Brielle,Ivy,Trinity,Josephine,Adalyn,Jocelyn,Emery,Adeline,Jordyn,Ariel,Everly,Lilly,Paige,Isla,Lyla,Makayla,Molly,Emilia,Mya,Kendall,Melody,Isabel,Mckenzie,Nicole,Payton,Margaret,Mariah,Eden,Athena,Amy,Norah,Londyn,Valeria,Sara,Aliyah,Angelina,Gracie,Rose,Rachel,Juliana,Laila,Brooklynn,Valerie,Alina,Reese,Elise,Eliza,Alaina,Raelynn,Leilani,Catherine,Cecilia,Genevieve,Daisy,Harmony,Vanessa,Adriana,Presley,Rebecca,Destiny,Julianna,Michelle,Adelyn,Arabella,Summer,Callie,Kaitlyn,Ryleigh,Lila,Daniela,Arya,Alana,Esther,Finley,Gabrielle,Jessica,Stephanie,Tessa,Makenzie,Ana,Amaya,Alexandria,Alivia,Nova,Anastasia,Iris,Marley,Fiona,Angela,Giselle,Kate,Alayna,Lola,Lucia,Juliette,Teagan,Sienna,Georgia,Hope,Cali,Vivienne,Izabella,Kinley,Daleyza,Kylee,Jayla,Katelyn,Juliet,Maggie,Delaney,Brynlee,Keira,Camille,Leila,Mckenna,Aniyah,Noelle,Josie,Jennifer,Melissa,Gabriela,Allie,Eloise,Jacqueline,Brynn,Evangeline,Ayla,Rosalie,Kali,Maci,Gemma,Lilliana,Raegan,Lena,Adelaide,Journey,Adelynn,Alessandra,Kenzie,Miranda,Haley,June,Charlee,Lucille,Talia,Skyler,Makenna,Phoebe,Jane,Lyric,Angel,Elaina,Adrianna,Ruth,Miriam,Diana,Mariana,Danielle,Jenna,Shelby,Nina,Madeleine,Elliana,Amina,Amiyah,Chelsea,Joanna,Jada,Lexi,Katie,Maddison,Fatima,Vera,Malia,Lilah,Madilyn,Amanda,Daniella,Alexia,Kathryn,Paislee,Selena,Laura,Annie,Nyla,Catalina,Kayleigh,Sloane,Kamila,Lia,Haven,Rowan,Ashlyn,Christina,Amber,Myla,Addilyn,Erin,Alison,Ainsley,Raelyn,Cadence,Kendra,Heidi,Kelsey,Nadia,Alondra,Cheyenne,Kaydence,Mikayla,River,Heaven,Arielle,Lana,Blakely,Sabrina,Kyla,Ada,Gracelyn,Allyson,Felicity,Kira,Briella,Kamryn,Adaline,Alicia,Ember,Aylin,Veronica,Esmeralda,Sage,Aspen,Gia,Camilla,Ashlynn,Scarlet,Journee,Daphne,Bianca,Mckinley,Amira,Carmen,Kyleigh,Megan,Skye,Elsie,Kennedi,Averie,Carly,Rylie,Gracelynn,Mallory,Emersyn,Camryn,Annabella,Elle,Kiara,Yaretzi,Ariella,Zara,April,Gwendolyn,Anaya,Baylee,Brinley,Sierra,Annalise,Tatum,Serena,Dahlia,Macy,Miracle,Madelynn,Briana,Freya,Macie,Helen,Bethany,Leia,Harlow,Jayleen,Angelica,Marilyn,Viviana,Francesca,Juniper,Carolina,Jazmin,Emely,Maliyah,Cataleya,Jillian,Joy,Abby,Malaysia,Nylah,Sarai,Evelynn,Nia,Zuri,Addyson,Aleah,Kaia,Bristol,Lorelei,Jazmine,Maeve,Alejandra,Justice,Julie,Marlee,Phoenix,Jimena,Emmalyn,Nayeli,Aleena,Brittany,Amara,Karina,Giuliana,Thea,Braelynn,Kassidy,Braelyn,Luciana,Aubrie,Janelle,Madisyn,Brylee,Amari,Eve,Millie,Kelly,Selah,Lacey,Willa,Haylee,Jaylah,Sylvia,Melany,Elisa,Elsa,Hattie,Raven,Holly,Aisha,Itzel,Kyra,Tiffany,Jayda,Michaela,Madilynn,Celeste,Lilian,Priscilla,Jazlyn,Karen,Savanna,Zariah,Lauryn,Alanna,Kara,Karla,Cassandra,Ariah,Evie,Aileen,Lennon,Charley,Rosemary,Danna,Regina,Kaelyn,Virginia,Hanna,Rebekah,Alani,Edith,Liana,Charleigh,Gloria,Colette,Kailey,Helena,Matilda,Imani,Bridget,Cynthia,Janiyah,Marissa,Johanna,Sasha,Kaliyah,Cecelia,Adelina,Jessa,Hayley,Julissa,Winter,Crystal,Kaylie,Bailee,Charli,Henley,Anya,Maia,Skyla,Liberty,Fernanda,Monica,Braylee,Mariam,Marie,Beatrice,Hallie,Maryam,Angelique,Anne,Madalyn,Alayah,Annika,Greta,Lilyana,Kadence,Coraline,Lainey,Mabel,Lillie,Anika,Azalea,Dayana,Jaliyah,Addisyn,Emilee,Mira,Angie,Lilith,Mae,Meredith,Guadalupe,Emelia,Margot,Melina,Aniya,Alena,Myra,Elianna,Caitlyn,Jaelynn,Jaelyn,Demi,Mikaela,Tiana,Shiloh,Ariyah,Saylor,Caitlin,Lindsey,Oakley,Alia,Everleigh,Ivanna,Miah,Emmy,Jessie,Anahi,Kaylin,Ansley,Annabel,Remington,Kora,Maisie,Nathalie,Emory,Karsyn,Pearl,Irene,Kimber,Rosa,Lylah,Magnolia,Samara,Renata,Galilea,Kensley,Kiera,Whitney,Amelie,Siena,Bria,Laney,Perla,Tatiana,Zelda,Jaycee,Kori,Montserrat,Lorelai,Adele,Elyse,Katelynn,Kynlee,Marina,Kailyn,Avah,Kenley,Aviana,Armani,Dulce,Alaia,Teresa,Natasha,Milani,Amirah,Breanna,Linda,Tenley,Sutton,Elaine,Aliza,Kenna,Meadow,Alyson,Milana,Erica,Esme,Leona,Joselyn,Madalynn,Alma,Chanel,Myah,Karter,Zahra,Audrina,Ariya,Jemma,Eileen,Kallie,Emmalynn,Lailah,Sloan,Clarissa,Karlee,Laylah,Amiya,Collins,Ellen,Hadassah,Danica,Jaylene,Averi,Reyna,Saige,Wren,Lexie,Dorothy,Lilianna,Monroe,Aryanna,Elisabeth,Ivory,Liv,Janessa,Jaylynn,Livia,Rayna,Alaya,Malaya,Cara,Erika,Amani,Clare,Addilynn,Roselyn,Corinne,Paola,Jolene,Anabelle,Aliana,Lea,Mara,Lennox,Claudia,Kristina,Jaylee,Kaylynn,Zariyah,Gwen,Kinslee,Avianna,Lisa,Raquel,Jolie,Carolyn,Courtney,Penny,Royal,Alannah,Ciara,Chaya,Kassandra,Milena,Mina,Noa,Leanna,Zoie,Ariadne,Monserrat,Nola,Carlee,Isabela,Jazlynn,Kairi,Laurel,Sky,Rosie,Arely,Aubrielle,Kenia,Noemi,Scarlette,Farrah,Leyla,Amia,Bryanna,Naya,Wynter,Katalina,Taliyah,Amaris,Emerie,Martha,Thalia,Christine,Estrella,Brenna,Milania,Salma,Lillianna,Marjorie,Shayla,Zendaya,Aurelia,Brenda,Julieta,Adilynn,Deborah,Keyla,Patricia,Emmeline,Hadlee,Giovanna,Kailee,Desiree,Karlie,Khaleesi,Lara,Tori,Clementine,Nancy,Ayleen,Estelle,Celine,Madyson,Zaniyah,Adley,Amalia,Paityn,Kathleen,Sandra,Lizbeth,Maleah,Aryana,Hailee,Aiyana,Joyce,Ryann,Caylee,Kalani,Marisol,Nathaly,Briar,Lindsay,Remy,Adrienne,Azariah,Harlee,Frida,Marianna,Yamileth,Chana,Kaya,Lina,Celia,Analia,Hana,Jayde,Joslyn,Romina,Anabella,Barbara,Bryleigh,Emilie,Nathalia,Ally,Evalyn,Bonnie,Zaria,Carla,Estella,Kailani,Rivka,Rylan,Paulina,Kayden,Giana,Yareli,Kaiya,Sariah,Avalynn,Jasmin,Aya,Jewel,Kristen,Paula,Astrid,Jordynn,Kenya,Ann,Annalee,Kiley,Marleigh,Julianne,Zion,Emmaline,Nataly,Aminah,Amya,Iliana,Jaida,Paloma,Asia,Louisa,Sarahi,Tara,Andi,Arden,Dalary,Aimee,Alisson,Halle,Aitana,Landry,Alisha,Elin,Maliah,Belen,Briley,Raina,Vienna,Esperanza,Judith,Faye,Susan,Aliya,Aranza,Yasmin,Jaylin,Kyndall,Saniyah,Wendy,Yaritza,Azaria,Kaelynn,Neriah,Zainab,Alissa,Cherish,Dixie,Veda,Nala,Tabitha,Cordelia,Ellison,Meilani,Angeline,Reina,Tegan,Hadleigh,Harmoni,Kimora,Ingrid,Lilia,Luz,Aislinn,America,Ellis,Elora,Heather,Natalee,Miya,Heavenly,Jenny,Aubriella,Emmalee,Kensington,Kiana,Lilyanna,Tinley,Ophelia,Moriah,Sharon,Charlize,Abril,Avalyn,Mariyah,Taya,Ireland,Lyra,Noor,Sariyah,Giavanna,Rhea,Zaylee,Denise,Janiya,Jocelynn,Libby,Aubrianna,Kaitlynn,Princess,Alianna';
*/


$girl_names = 'Yury Korn,Kelly Dahl,Vivian Santiago,Amoreena Hall,Molly Steinberg,Tomi Rostén,Angel Hernandez,Tommi Saunter-Chun,Janis Vigulis,Ingrid Marti,Niki Loipold,Kalle Peltonen,Andrea Corsetti,Matti Huotari,Matti Klami,Matti Koskinen,Kari Kalen,Klaudia Monica,Tommi Nieminen,Amber Laybourne,Elis Jackson,Lindsay Barrie,Julia Pros Albert,Ariel Hartung,Hannah Lewis,Jone Randa,Kelsey Roach,Andrea Fraccalvieri,Angel Pina,Reese Starowesky,Tomi Virtanen,Gillian Woods,Kimber Jansen,Skylar Dunning,Stefanie Fleper,Angel Felipe Nuño Garcia,Bas Westerik,Tomi Salmi,Lauri Haverinen,Cherie Mollohan,Conny Naslund,Remy Lozza,Remy Provencher,Floris Bieshaar,Kalle Vaimala,Marjan Koderman,Tomi Hannuksela,Jani Kankaanpää2,Andrea Frollo,Sage Karam,Sara Dove,Angel Broceno,Denise Hallion2,Ellen VanNest2,Anne Struijk,Angel Lozano,Sian Walters,Kari Kiviluoto,Alyssa Ferrie,Tomi Mairue,Gwen Kolsteren,Dalila Magdu,Andrea Stefanoni,Carmen Comeau,Andrea Del Piccolo,Nelli Pietro,Jennifer Grifhorst,Lari Niskala,Pier Andrea Cappelletti,Tommi Seppänen,Sabrina Gramß,Laura Zampa,Angel Rosa2,Susi Badia,Chandra Wahyudi Jong,Jocelyn Lauzière,Sasha Ebel,Rus Marius,Lauri Mattila,Andrea Masiero,Andreia Azevedo,Sena Pugh,Kari Koski,Montserrat Cervera,Kelly Knight,Ally Sinclair,Lisette Davey,Matti Mäki,Bas Metselaar,Renie Silveira Marquet Filho,Kaya Selcuk,Niki Djakovic,Matti Räty,Tomi Ojala,Jani Petäjäniemi,Sindy Lajoie,Alison Willian Gomes Dos Santos,Niki Kresse,Agnes B Kaiser,Shelby Blackstock,Leila Hammadouche,Kari V. Saastamoinen,Dian Kostadinov,Tommi Piekäinen,Lindsay Wauchop,Reiko Arnold,Myung Kun Shin,Sofia Stella,Kari Ontero,Pascale Paquet,Joyce Martin,Holly Crilly,Sunday Awoniyi,Andrea Melonari,Jani Nurminen,Maris Sulcs,Jessica Dube2,Andrea Lo Presti Costantino,Delaney Mulholland,Amal Youness,Tomi Leino,Andrea Donati,Bas Rowinkel,Kalifa Dong,Jonni Molyneux,Jani Vähäsöyrinki,Hedwig Gager,Tommi Ojala,Lark Mint,Ilham Halabi,Ronni Brian,Kalle Lehtonen,Kalle Uusitalo,Nathalie Gaubert,Andrea Rizzoli,Angel Saez Juan,Andrea Zecchetti,Georgi Dimov,Andrea Schilirò,Angel Banegas,Niki Jessen,Jani Mikkola,Angel Hernandez Corte,Kimm Johansson,Ainsley Martin,Mary Ford,Tommi Rantala,Kari Kuhmonen,Remington Hudson,Jenny Balzer,Laura Perez Garcia,Gia Gogolashvili,Avery Thompson,Tommi Terävä,Matti Wolff,Josefa Jaeger,Cristi Harjoi,Lari Salminen,Tommi Jaakonsaari,Sadie Figueroa,Jani Nurmi,Jani Holopainen,Matti Kortelainen,Jani Jarvikivi,Shakira Dickson,Isabel Catala,Ariel Gomez Menas,Andrea D&#039;&#039;Amicis,Laren Swaby,Niki Croukaert,Tomi Halmetoja,Lauryn Brown,Andrea J Nicolis,Jasmin Dizdarević,Carmen Villafáñez Uña3,Wendy Crozier,Jocelyn Prévost2,Angel Perdiguero Roldan,Skyler Karanik,Ife Olukoya,Pascale Lievens,Skyler Rivera2,Katarzyna Cajs,Floris van Poele,Kari Korvala,Adria Huguet Casas,Pier Paolo Spinelli,Jennifer Denis,Nur Abdul Kadir,Daniela Taubert,Rowan Gill,Kelly Stoops,Heidi Ammari,Alin Dumitru,Hang Ho TAO,Alison Hine,Sloan Markey,Kelsie Chisholm,Andrea Melonari2,Skyler Sisson,Floris Smit,Mikiya Mizui,Angel Rey,Karen Lambourne,Angel Podadera Paradas,Sofija Eftimovski-Bozovic,Alison Marshall,Si Thomas,Yanick Sluiter,Andrea Rossopinti,Nicki Thiim,Andrea Albertinelli,Andrea Perazzi,Andrea Cremonesi,Bas Smeets2,Lala Isasi Rion,Miki Casanovas,Bas Roding,Jerrye Barriger2,Kari Tuovinen,Reese Boisse,Andrea Fontana,Maris Sudniks,Jani Tähtinen,Mary Goins,Remy Bretagnolle,Ellinor Ström,Angel Goce Cabreira,Remy Tremblay,Lauri Kongas,Angel Sanchez-Mayoral Gonzalez,Vivien Caplat,Casi Martinez,Marilena Di Ridolfi,Sharon E Muto,Ansleigh Davidson,Lauri Nykanen,Tommi KotijÃ¤rvi,Kari Kankaala,Marison Uratani,Lauri Posio,Andrea Filippini,Angel Ropero,Kalle Toivanen,Angel Gomez Garcia,Andrea Veschi,Andrea Giovale,Claudia Vogt,Margaret Frank,Beatrice Schmalz-Engelsmann,Vania Mascioni,Michaela Loeffler,Jani Siurua,Courtney Tate,Janis Ziedins,Andrea Ventura,Michelle Savy,Wendy Sarrett,Jani Kultanen,Tomi Nousiainen,Floris Koen,Kelsey Jones,Laura Dalmau,Mirka Lindström,Claudia Tellechea,Andrea Tannino,Emory Allen,Jani Rutanen,Andrea Carlisi,Ana Laforgue,Raine Riihimäki,Tommi Riipinen,Svetlana Dorokhova,Andrea La colla,Patricia Pasciolla,Andi Mann,Julie Robertson,Chantel Gee-Mackrill,Alicia Avendaño Marín,Jennifer Barroso Ledo,Mardi Howe,Andrea Tramontozzi,Lynette Markham,Kayleen Hoy,Kalle Midbeck,Rowan J.D Ford,River Whisper,Sandra Palm,Lauri Mäkitalo,Elis Zidaric,Sakari Pesonen,Whitney Strickland,Kristen Barra,Valery Vodchits,Shawna Shade,Andrea Carpene,Zenja Van der Ster,Nicol Foggie,Ariel Guerra JR,Angel Diaz,Camille Younan,Mali Francis,Andrea Cribioli,Tomi Ikonen,Heather Douglas,Ariel Couso,Matti Saloranta,Carmen Bernardo,Vianet Jerome,Tomi Holtta,Ellis Aldridge,Andrea Forneris,Victoria Champion,Brittany Grimm,Tommi Rautanen,Andrea Orsucci,Leanne Cordon,Katja Wolf,Nichola Pullen,Azhar Iqbal,Susan Allison2,Toshi Tanaka,Doni Pereira,Lina Raggi,Kari Painio,Tomi Kakkonen,Amber Stiffler,Karri Keskinen,Tomi Saarinen,Bas Visser,Camilla Deri,Nayra Monte Soto,Maria Engström,Lila Belmokhtar,Sara Savage,Bas Bouma,Tomi Määttä,Anne Medema,Andrea Mantovani3,Andrea Baldini,Camryn Edwards,Jocelyn Mouton,Andrea Iozzia,Lauri Mäntyvaara2,Tere Sammallahti,Jennifer McDonald,Sara Black,Abby Harris,Yui Tabata,Michelle Kehlenbeck,Jani Laakkonen,Pier Luigi Alessio,Yury Vaskow,Maria Hollweck,Chelsea Angelo,Aubrey Horn,Andrea Carraro,Sanna Unnersjö,Angel Barreda Calvo,Yury Porozov,Lauri Koskinen,Angel Roglan Beltri,Angela R Coleman,Nadine Sander,Lana Opačak,Erin Miller,Andrea Leonardis,Matti Liehu,Rathana Danh Sang2,Andrea Zanoni,Maja Ljunggren,Andrea Marzona,Georgi Raychev,Lauri Perlström,Kelly Gingras,Elodie Hannequin,Saskia Schmidt,Chrystle Jones,Adria Serratosa,Andrea Favini,Remy Ammour,Gila Dezso,Vesna Paternoster,Kristina Matagić,Lucia Esposito,Kendall Baumann,Tommi Flod,Alin Albulescu,Emily Coates,Angel L. Lahoz,Christine Valencia,Celia Sousa,Matti Hoffmann,Jocelyn Pellé,Tomi Hurme,Jennifer Heintzschel,Ruba Oliva,Andrea Gallo2,Vanesa Beatriz Martinez,Jessie Eduardo Hemink2,Miki Voj,Flo Kremser,Sarah Toplis,Oakley Peterson,Angel Bajo,Camille Caytan,Angel L Rodriguez Alcalde,Jani Saarelainen,Indra Feryanto,Yury Komusau,Rosa Maria Iglesias Diaz,Kari Uotila,Sabrina Enting,Ariel Varro,Yury Smolsky,Lauri Lään,Bas de Weert,Andrea Kienbauer,Anne de Vries,Andrea Berti,Jani Kopra,Andrea Boarini,Remy Thendrup,Emili Mulet,Addy Wood,Camille Spillman,Fiona Binney,Maria Bako,Jannis van der Eyck,Jessie Lan,Kendall Davis,Maria Stuart2,Jocelyn Brassaud,Galina Bruksh,Conny Lundell,Inge Hansesætre,Andrea Guerrini,Tommi Rahkila,Paw Schou,Yury Luzan,Andi Sentkowski,Estefania Rubio Santiago,Jannis Mueller,Kari Lahtinen,Anne Schwendiman,Jennifer Parks2,Janis Balodis,Linde Johnson,Dian Dechev,Yanick Aubin,Jocelyn Bellemare,Tomi Kokkola,Jojo Ak,Janis Braslins,Leen Melissant,Edwina Van der haegen3,Angel Huerta Gomez';
$girl_names = explode( ',', $girl_names );
foreach ( $girl_names as $key => $name ) {
	$name = explode( ' ', $name );
	if (
		'Tommi' !== $name[0]
		&&
		'Jani' !== $name[0]
		&&
		'Pier' !== $name[0]
		&&
		'Andrea' !== $name[0]
		&&
		'Kari' !== $name[0]
		&&
		'Matti' !== $name[0]
		&&
		'Josefa' !== $name[0]
		&&
		'Bas' !== $name[0]
		&&
		'Sakari' !== $name[0]
		&&
		'Tomi' !== $name[0]
		&&
		'Jone' !== $name[0]
		&&
		'Amal' !== $name[0]
		&&
		'Azhar' !== $name[0]
		&&
		'Yury' !== $name[0]
		&&
		'Amal' !== $name[0]
		&&
		'Kalifa' !== $name[0]
		&&
		'Lark' !== $name[0]
		&&
		'Kelly' !== $name[0]
		&&
		'Kelly' !== $name[0]
		&&
		'Kelly' !== $name[0]
		&&
		'Kelly' !== $name[0]
		&&
		'Kelly' !== $name[0]
		&&
		'Kelly' !== $name[0]
		&&
		'Kelly' !== $name[0]
	) {
		$girl_names[$key] = $name[0];
	}
}
$girl_names = array_unique( $girl_names );
//print_r( $girl_names );die;
//echo count( $girl_names );die;

// Get iRacing stats
$dir = wp_upload_dir();
$stats = file_get_contents( $dir['basedir'] . '/iracing-members.json' );
$stats = json_decode( $stats, true );

$count = 0;
foreach ( $stats as $name => $stat ) {
	$n = explode( ' ', $name );

	if ( in_array( $n[0], $girl_names ) ) {

		if ( isset( $stat['road_irating'] ) && '-1' !== $stat['road_irating'] ) {
			if (
				1000 < $stat['road_irating']
				&&
				(
					'A' === $stat['road_license']
					||
					'B' === $stat['road_license']
					||
					'C' === $stat['road_license']
				)

			) {
				$count++;
				if ( 'irating' === $_GET['girls'] ) {
					echo $name . ': iRating - ' . $stat['road_irating'] . "\n";
				} else {
					echo $name . ",";
				}
			}

		}
	}
}
echo $count;
die;