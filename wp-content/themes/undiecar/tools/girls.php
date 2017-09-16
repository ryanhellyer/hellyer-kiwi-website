<?php

if ( ! isset( $_GET['girls'] ) ) {
	return;
}


$girl_names = 'Emma,Olivia,Sophia,Ava,Isabella,Mia,Abigail,Emily,Charlotte,Harper,Madison,Amelia,Elizabeth,Sofia,Evelyn,Avery,Chloe,Ella,Grace,Victoria,Aubrey,Scarlett,Zoey,Addison,Lily,Lillian,Natalie,Hannah,Aria,Layla,Brooklyn,Alexa,Zoe,Penelope,Leah,Audrey,Savannah,Allison,Samantha,Nora,Skylar,Camila,Anna,Paisley,Ariana,Ellie,Aaliyah,Claire,Violet,Stella,Sadie,Mila,Gabriella,Lucy,Arianna,Kennedy,Sarah,Madelyn,Eleanor,Kaylee,Caroline,Hazel,Hailey,Genesis,Kylie,Autumn,Piper,Maya,Nevaeh,Serenity,Peyton,Mackenzie,Bella,Eva,Naomi,Aubree,Aurora,Melanie,Lydia,Brianna,Ruby,Katherine,fCassidyAlice,Cora,Julia,Madeline,Faith,Annabelle,Alyssa,Isabelle,Vivian,Gianna,Quinn,Clara,Khloe,Alexandra,Eliana,Sophie,London,Elena,Kimberly,Maria,Luna,Willow,Jasmine,Kinsley,Kayla,Delilah,Andrea,Natalia,Lauren,Rylee,Adalynn,Mary,Ximena,Jade,Liliana,Brielle,Ivy,Trinity,Josephine,Adalyn,Jocelyn,Emery,Adeline,Jordyn,Ariel,Everly,Lilly,Paige,Isla,Lyla,Makayla,Molly,Emilia,Mya,Kendall,Melody,Isabel,Mckenzie,Nicole,Payton,Margaret,Mariah,Eden,Athena,Amy,Norah,Londyn,Valeria,Sara,Aliyah,Angelina,Gracie,Rose,Rachel,Juliana,Laila,Brooklynn,Valerie,Alina,Reese,Elise,Eliza,Alaina,Raelynn,Leilani,Catherine,Cecilia,Genevieve,Daisy,Harmony,Vanessa,Adriana,Presley,Rebecca,Destiny,Julianna,Michelle,Adelyn,Arabella,Summer,Callie,Kaitlyn,Ryleigh,Lila,Daniela,Arya,Alana,Esther,Finley,Gabrielle,Jessica,Stephanie,Tessa,Makenzie,Ana,Amaya,Alexandria,Alivia,Nova,Anastasia,Iris,Marley,Fiona,Angela,Giselle,Kate,Alayna,Lola,Lucia,Juliette,Teagan,Sienna,Georgia,Hope,Cali,Vivienne,Izabella,Kinley,Daleyza,Kylee,Jayla,Katelyn,Juliet,Maggie,Delaney,Brynlee,Keira,Camille,Leila,Mckenna,Aniyah,Noelle,Josie,Jennifer,Melissa,Gabriela,Allie,Eloise,Jacqueline,Brynn,Evangeline,Ayla,Rosalie,Kali,Maci,Gemma,Lilliana,Raegan,Lena,Adelaide,Journey,Adelynn,Alessandra,Kenzie,Miranda,Haley,June,Charlee,Lucille,Talia,Skyler,Makenna,Phoebe,Jane,Lyric,Angel,Elaina,Adrianna,Ruth,Miriam,Diana,Mariana,Danielle,Jenna,Shelby,Nina,Madeleine,Elliana,Amina,Amiyah,Chelsea,Joanna,Jada,Lexi,Katie,Maddison,Fatima,Vera,Malia,Lilah,Madilyn,Amanda,Daniella,Alexia,Kathryn,Paislee,Selena,Laura,Annie,Nyla,Catalina,Kayleigh,Sloane,Kamila,Lia,Haven,Rowan,Ashlyn,Christina,Amber,Myla,Addilyn,Erin,Alison,Ainsley,Raelyn,Cadence,Kendra,Heidi,Kelsey,Nadia,Alondra,Cheyenne,Kaydence,Mikayla,River,Heaven,Arielle,Lana,Blakely,Sabrina,Kyla,Ada,Gracelyn,Allyson,Felicity,Kira,Briella,Kamryn,Adaline,Alicia,Ember,Aylin,Veronica,Esmeralda,Sage,Aspen,Gia,Camilla,Ashlynn,Scarlet,Journee,Daphne,Bianca,Mckinley,Amira,Carmen,Kyleigh,Megan,Skye,Elsie,Kennedi,Averie,Carly,Rylie,Gracelynn,Mallory,Emersyn,Camryn,Annabella,Elle,Kiara,Yaretzi,Ariella,Zara,April,Gwendolyn,Anaya,Baylee,Brinley,Sierra,Annalise,Tatum,Serena,Dahlia,Macy,Miracle,Madelynn,Briana,Freya,Macie,Helen,Bethany,Leia,Harlow,Jayleen,Angelica,Marilyn,Viviana,Francesca,Juniper,Carolina,Jazmin,Emely,Maliyah,Cataleya,Jillian,Joy,Abby,Malaysia,Nylah,Sarai,Evelynn,Nia,Zuri,Addyson,Aleah,Kaia,Bristol,Lorelei,Jazmine,Maeve,Alejandra,Justice,Julie,Marlee,Phoenix,Jimena,Emmalyn,Nayeli,Aleena,Brittany,Amara,Karina,Giuliana,Thea,Braelynn,Kassidy,Braelyn,Luciana,Aubrie,Janelle,Madisyn,Brylee,Amari,Eve,Millie,Kelly,Selah,Lacey,Willa,Haylee,Jaylah,Sylvia,Melany,Elisa,Elsa,Hattie,Raven,Holly,Aisha,Itzel,Kyra,Tiffany,Jayda,Michaela,Madilynn,Celeste,Lilian,Priscilla,Jazlyn,Karen,Savanna,Zariah,Lauryn,Alanna,Kara,Karla,Cassandra,Ariah,Evie,Aileen,Lennon,Charley,Rosemary,Danna,Regina,Kaelyn,Virginia,Hanna,Rebekah,Alani,Edith,Liana,Charleigh,Gloria,Colette,Kailey,Helena,Matilda,Imani,Bridget,Cynthia,Janiyah,Marissa,Johanna,Sasha,Kaliyah,Cecelia,Adelina,Jessa,Hayley,Julissa,Winter,Crystal,Kaylie,Bailee,Charli,Henley,Anya,Maia,Skyla,Liberty,Fernanda,Monica,Braylee,Mariam,Marie,Beatrice,Hallie,Maryam,Angelique,Anne,Madalyn,Alayah,Annika,Greta,Lilyana,Kadence,Coraline,Lainey,Mabel,Lillie,Anika,Azalea,Dayana,Jaliyah,Addisyn,Emilee,Mira,Angie,Lilith,Mae,Meredith,Guadalupe,Emelia,Margot,Melina,Aniya,Alena,Myra,Elianna,Caitlyn,Jaelynn,Jaelyn,Demi,Mikaela,Tiana,Shiloh,Ariyah,Saylor,Caitlin,Lindsey,Oakley,Alia,Everleigh,Ivanna,Miah,Emmy,Jessie,Anahi,Kaylin,Ansley,Annabel,Remington,Kora,Maisie,Nathalie,Emory,Karsyn,Pearl,Irene,Kimber,Rosa,Lylah,Magnolia,Samara,Renata,Galilea,Kensley,Kiera,Whitney,Amelie,Siena,Bria,Laney,Perla,Tatiana,Zelda,Jaycee,Kori,Montserrat,Lorelai,Adele,Elyse,Katelynn,Kynlee,Marina,Kailyn,Avah,Kenley,Aviana,Armani,Dulce,Alaia,Teresa,Natasha,Milani,Amirah,Breanna,Linda,Tenley,Sutton,Elaine,Aliza,Kenna,Meadow,Alyson,Milana,Erica,Esme,Leona,Joselyn,Madalynn,Alma,Chanel,Myah,Karter,Zahra,Audrina,Ariya,Jemma,Eileen,Kallie,Emmalynn,Lailah,Sloan,Clarissa,Karlee,Laylah,Amiya,Collins,Ellen,Hadassah,Danica,Jaylene,Averi,Reyna,Saige,Wren,Lexie,Dorothy,Lilianna,Monroe,Aryanna,Elisabeth,Ivory,Liv,Janessa,Jaylynn,Livia,Rayna,Alaya,Malaya,Cara,Erika,Amani,Clare,Addilynn,Roselyn,Corinne,Paola,Jolene,Anabelle,Aliana,Lea,Mara,Lennox,Claudia,Kristina,Jaylee,Kaylynn,Zariyah,Gwen,Kinslee,Avianna,Lisa,Raquel,Jolie,Carolyn,Courtney,Penny,Royal,Alannah,Ciara,Chaya,Kassandra,Milena,Mina,Noa,Leanna,Zoie,Ariadne,Monserrat,Nola,Carlee,Isabela,Jazlynn,Kairi,Laurel,Sky,Rosie,Arely,Aubrielle,Kenia,Noemi,Scarlette,Farrah,Leyla,Amia,Bryanna,Naya,Wynter,Katalina,Taliyah,Amaris,Emerie,Martha,Thalia,Christine,Estrella,Brenna,Milania,Salma,Lillianna,Marjorie,Shayla,Zendaya,Aurelia,Brenda,Julieta,Adilynn,Deborah,Keyla,Patricia,Emmeline,Hadlee,Giovanna,Kailee,Desiree,Karlie,Khaleesi,Lara,Tori,Clementine,Nancy,Ayleen,Estelle,Celine,Madyson,Zaniyah,Adley,Amalia,Paityn,Kathleen,Sandra,Lizbeth,Maleah,Aryana,Hailee,Aiyana,Joyce,Ryann,Caylee,Kalani,Marisol,Nathaly,Briar,Lindsay,Remy,Adrienne,Azariah,Harlee,Frida,Marianna,Yamileth,Chana,Kaya,Lina,Celia,Analia,Hana,Jayde,Joslyn,Romina,Anabella,Barbara,Bryleigh,Emilie,Nathalia,Ally,Evalyn,Bonnie,Zaria,Carla,Estella,Kailani,Rivka,Rylan,Paulina,Kayden,Giana,Yareli,Kaiya,Sariah,Avalynn,Jasmin,Aya,Jewel,Kristen,Paula,Astrid,Jordynn,Kenya,Ann,Annalee,Kiley,Marleigh,Julianne,Zion,Emmaline,Nataly,Aminah,Amya,Iliana,Jaida,Paloma,Asia,Louisa,Sarahi,Tara,Andi,Arden,Dalary,Aimee,Alisson,Halle,Aitana,Landry,Alisha,Elin,Maliah,Belen,Briley,Raina,Vienna,Esperanza,Judith,Faye,Susan,Aliya,Aranza,Yasmin,Jaylin,Kyndall,Saniyah,Wendy,Yaritza,Azaria,Kaelynn,Neriah,Zainab,Alissa,Cherish,Dixie,Veda,Nala,Tabitha,Cordelia,Ellison,Meilani,Angeline,Reina,Tegan,Hadleigh,Harmoni,Kimora,Ingrid,Lilia,Luz,Aislinn,America,Ellis,Elora,Heather,Natalee,Miya,Heavenly,Jenny,Aubriella,Emmalee,Kensington,Kiana,Lilyanna,Tinley,Ophelia,Moriah,Sharon,Charlize,Abril,Avalyn,Mariyah,Taya,Ireland,Lyra,Noor,Sariyah,Giavanna,Rhea,Zaylee,Denise,Janiya,Jocelynn,Libby,Aubrianna,Kaitlynn,Princess,Alianna';
$girl_names = explode( ',', $girl_names );

// Get iRacing stats
$dir = wp_upload_dir();
$stats = file_get_contents( $dir['basedir'] . '/iracing-members.json' );
$stats = json_decode( $stats, true );

foreach ( $stats as $name => $stat ) {
	$n = explode( ' ', $name );

	foreach ( $girl_names as $key => $girl_name ) {
		if ( $girl_name === $n[0] ) {
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
					echo $name . ': iRating - ' . $stat['road_irating'] . "\n";
				}
			}
		}

	}

}

die;