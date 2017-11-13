<?php

if ( ! isset( $_GET['pull_names'] ) ) {
	return;
}


define( 'ALLOW_ROOKIES', true );
define( 'ALLOW_B_LICENSES', true );
define( 'ALLOW_C_LICENSES', true );
define( 'ALLOW_D_LICENSES', true );

//define( 'MIN_OVAL_IRATING', 3000 );
//define( 'MIN_ROAD_IRATING', 2000 );
define( 'MIN_OVAL_IRATING', 400 );
define( 'MIN_ROAD_IRATING', 400 );



/*****
 ****** IMPORTANT:
 ****** use GET_USERS() to CHECK FOR EXISTING USERS - needs new code for this
 ******/

// sent
$current_signups = '';
foreach ( get_users() as $key => $driver ) {
	$driver_id = $driver->ID;
	$current_signups .= $driver->data->display_name . ',';
}
$personal_contacted = 'Robert Plumley,Ken Ehlert,Louis Richardson,Andre Moreira,Ben Wheeler,Beto Soussa,Bill Sulouff,Brandon Johnson8,Bruno Romain,Claudius Wied,Craig Crawford,Craig P Kasper,Daniel Wright4,Dave Lodl,Dennis Sather,Dominic Hoogendijk,Floyd Pate,Glen Barrett,Guilherme Carioni,James Craig4,Jamie Brinkley,Jeff Meier,Jelle Verstraeten2,Jose E. Piña,Jose Serrano,Juan Payano2,Kevin McCarthy,Kyle Schuchter,André Heidstra,Luigi Griffini,Marcello Caruso,Marcos Antonio2,Mark A Reed,Mark Voigt,Matthew Randle,Michael Johnson21,Montxo Gandia,Morten Hansen,Neil A. Jackson,Nikolay Ladushkin,Patrick Langley,Paul Rosanski,Ramon Regalado,Randy Parker,Ron Lanzafame,Steven Busuttil,Stuart Lumpkin,Szabolcs Feher,Tom Ecklein,William Norton,Zachary Luctkar,';
$indycar_road_drivers = "Marco Aurelio Brasil,Adam Plunkett,Karsten Brodowy,John Downing,Tim Holgate,Georg Naujoks,Kent Turnbull,Alexander Khursanov,Gary Powell3,A J Burton,chad Trumbla,Per-Anders Mårtensson,Bradley R Smith,Andrey Efimenko,Michele Costantini,David Adams8,Niall McBride,Andrew Kinsella,Matthew Talbert,David Hinz II,Justin Kay,Carl Johnson3,Austin Espitee,Antenor Junior2,Frederick Campbell,Andrew Stone,Serge Cantin,Karl Dronke,Sergio Morresi,Dan Lee Ensch,Rudy Avalon2,Dylan McKenna,Silviu Lazar,Bradley Walters,Andreas Eik,Jean-Marc Brunée,Anthony Cothran,Yves Bolduc,Todd Novasad,Ricardo Rossi,Wade Lear,David Warhurst,Steven Landis,Tom Rowin,Ben Ivaldi,Dimitri Djukanovic,Steves Arvisais,Christopher Hoyle,Travis Bennett,Kevin Sherker2,Tim Williams,Rados?aw Sitarz,Eric Ward2,Chris Stofer,Evan Fitzgerald,Kevin Cornelius,Yuichiro Takahara,John Lott,Zack Ditto,Josh Frye2,Francesco Sollima,Jeramie Horn,Dakota Steven Bowman,John Ahles,Joel Feytout,Andrew Jones6,Nikolay Bogatyrev,Carl Jacolette,Matt Denlinger,Tyrone Harris,Andrew Faryniarz,Tom Kotowski,Jordi Ardid Mendez,Christopher Brown4,Ryan Wilson6,Allan Moreira,Philipp Wigert,M B Dickey2,Henry Bennett,Fernando Guerrero,Rikki Gerhardt,Guillermo Domínguez,Andrea Antongini,Jussi Nieminen,John Burgess,Travis Parker,Dan Barone,Rob Powers,Santiago Nahuel Monente,Guillermo Alvarez,Enric Cabral,Harald Müller,David Henger,Kevin Holzner,JW Miller,Adam Roberson,Thomas Anton Leitgeb,John Merchant,Joachim Brückner,Luis Piñero,Henry White,Pedro Zoffoli,Lincoln Miguel,Richard Browell,Andrew Massey,Christian Steele,Rob Unglenieks,Richard Tam,Troy Eddy,Stephen Warcup,David P Pérez,Andrew Fullhart,Naruko Ishida,Bryan Carey,Doc Stout,Darren Adams2,Thomas Marmann,Richard Kaz,Neil Black,Steven Walter,Francisco Rendo,Casey Drake,Milton Thigpen,Jay Davis4,Wilson Rachid,Drew Motz,Chris Ferry2,Cesare Di Emidio2,Bernhard Jansen2,Trey Mccrickard,Jay Norris,Samuel Etter,Scott McClendon,Kleber Bottaro Moura,Jason Mayberry,Darryn Hatfield,Marcus Wohlmuth,Bob Jennings,Anthony Emery,Samuel Zinski,Pierre Bourdon,John Hess,Philip Eckert,Kaue Gomes,Mitchell Kerstetter,Mitch Walter,William Ruland,Cristian Perocarpi,Daniel Wester,David Riley2,Benton Jones,Albert Oldendorp,Adriano Fraporti,Wolf-Dietrich Hotho,Andrew Nypower,Tom Orr,Steven Clouse,Brian Beard,Rayyan Rawat,John Mignacca,José Godoy,Roger Proctor,Nate LaFluer,Harold L Stevens III,Ronnie Osmer,Brandon Trost,Arnold Estep,Tanner McCullough,Ian Layne,Joshua Witherspoon,Adam Baker5,Juan Riveros Puentes,Patrick Pierce,Blake O''Connell,Dave Jinks,Christopher Demeritt,Joe Branch2,Zach Reinke,Tim Doyle,Alesander Rodrigo,Andreas Werner,James E Davis,Brandon Clarke,Joey Bolufe,Adam Cavalla,Ken Owsley,Bruno Pagiola de Oliveira,Carlos Washington,Jan Penicka,Radek Sykora,Brandon A Taylor,Jeffrey Lacey,Joseph Wheatley,Petri Välilä,Dean Mullins,Joe Burchett,Julian Lavarias,Teemu J Rönkkö,Jason Brassfield,Cristian Otarola,Jared Wishon,Ryan Nolan,Timothy Allen2,Patrick Hingston,Matthew Montis,Carlos Neto,Christian Cabangca,Troy Thiem,Jens Roecher,Patrick Byrne,Kai Tröster,Justin Fortener,Alexander Knisely,Tyler Langenberg,Stephane Parent,Joonas Kortman,Jefferson Padovani,Kim Short,Said Gonzalez,Caeton Bomersbach,Jorge Marquinez,Olivier Dean2,Rodrigo Azevedo,Humberto Rattmann,Marco A Pereira,Wayne Sanderson,Alex Everitt,David Altman,Jeff Pritchard,Ryan Junge,Geoffrey Cervellini,Ludovic Mostacchi3,Ralf Schmitt,Nicolas Guarino,Mirco Comitardi,Anthony Gardner3,Julian Wörner2,James Robinson11,Andrew Aitken,Ronald Goodison,Tracy Drummond,Bob Martin,Jason Cange,Jeremy Hartman,Mertol Shahin,Stuart McPhaden,Peter Grey,Tim Kay,Oliver Elsen,Soeren Kolodziej,Mads B L Hansen,Jacob Yuenger,Darren Leslie,Stefan Remedy,Julian Wagg,Federico Calderon,Felipe Kaplan,Ernes Romero,Andrew S Brown,Douglas Rice,Richard da Silva,Jagoba Merino4,Jerry Foehrkolb,Todd Schneller,Joseph Plante,John Garrett,Trevor Avery,Migeon Johnny,Radoslaw Ciszkiewicz,Bill Fisher,Jake Henry,Clifford Ebben,Kian Raleigh-Howell,William Stroh,Christopher Mattas,Luis Pedreros,Jordan Lubach,J Santos,Ryan Lewis,Kyle Selig,Carlos Buritica,James Kilburn,Colin Appleton,Adam Hackman,Nico Rondet,Rodrigo Munhoz,David Corpas Benitez,Derin Pitre,Neil Andrews2,John Keefe,Andrew Cauffiel,Ryan Stein,James Woods3,Trevor Fitz,Arthur Rast,Karl Schwing,Mike A Taylor,Chris Reid,Mikey Olson,Brendan Juno,Robert Nuernberg,Timothy Roberts,Javier Garrido Vaquero,Leonardo Marques,Victor M Cano,Alexandre Martins G,Luis Sereix,Tyler Worrall,Lucas Stattel,Erick Davis,Garrett Konrath,Lucas Louly,Scott Faris,Bobby Maiden III,Brian McCraven,Andy Chadbourne,Tommy Farris,Alexandre Vinet,Jeff Yeager,Mar Vozer Felisberto,David Ross,Heamin Choi,Gage Rivait,Guillaume Pelletier,Anthony Mino,Douwe Tapper,Sam Adams,Davis Trask,Gonzalo Romero,Jacob Phillips,Michael P McVea,Justin Weaver,Brenden John Koehler,John-Paul Bonadonna,Tyler Stacy,Collin R Stark,Nicholas Soriano2,Donovan Piper,Joey Lamm,Matt M Adams,John M Roberts,Jacob Gordon,Simone Nicastro,Martijn Nagelkerken,Corey J Ott,Ricky Heinan,Marco Colasacco,Andy Crane,Sergio Lamares,Jim Flippin,Scott Beck,Paul Parashak,Brian Rainville,Yanisse Ameurlaine,Senad Kocan,Charles Crump,Blair Hamrick5,Todd Broeker,Pat Copley,Jordan Pruden,Nicholas Schmieg,Jacob Young,Andres Espinoza,Justin Parcher,Lee Hamlet,Jacob Schneider,Massimo Duse,Austin H Blair,James Pandolfe III,Ross Olson,Andy Rhodes,Christopher Hussey2,Tobias Brown,";
$laguna_seca_dallara_dash_drivers = 'Joshua S Lee,Craig Shepherd,Derek Hartford,Kevin Vogel,Eneric Andre,Alex Bosl,John Dubets,Jimmy Duncan,Albert Gisbert Falgueras,Sam Rosamond,Elliott Skeer,Robert Ulff,Oliver Patock,Robert Draper,Bruce Granheim,Mike Medley,Yusef Rayyan,John Szpyt,Nash Fry2,Barry Arends,Collin van Raam,Carl Modoff,Chris Meeth,Anver Larson,Alex Millward,Craig Evanson,Gulas Mate,Claes Poulsen,Tomasz Kordowicz,Maurice Gomillion,Brett Gardner,James Osborne4,Sepp Odoerfer,Kevin Hollinger,Craig Forsythe2,John Ellison,David Bessa Dias,Benjamin Cox,Rebelo Romain,Jeroen van Wissen,Arjan de Vreed,Gary Krichbaum2,Jonathan Heimbach,Nicholas Millard,Jake Hewlett,Alain Stoffels,Attila Papp Jr,Eddy Andersson2,Gabriel Garcia Olivares,James Strobel II,Kevin Giménez,Thibault CAZAUBON7,Derek Adams,Joris Valentin2,Robert Siegmund,Hildebrando Pinho Junior,Michael D Myers,Ben Clayden,Rob Donoghue,Elias Viejo,John Signore,Davis Rochester,Alexandre Gramaxo Gouveia,Jacob Bieser,Susan Blackledge,Robert B Eriksson,Alexandre Gravouille,Cody Siegel,Jan Spamers,Richard Sudduth,Szilard Halaszi,Shaun Barrowcliffe,Anton Kusmenko,Pierre Verne,Ryan Schartau,Florian Bayle,Vincent Hamet,Guillaume Dupont2,Imre Lukacs,Romain Pelissier,Xisco Fernández,Loris Amadio,Benjamín Carreiras,Andre Monteiro2,Robert Long2,Braden Graham,Duncan Watt,Paul J Ulliott,Tom Berendsen,David Strickland,Antonio Bermúdez,Scott Nicholson Jr,Adolf Egli,Sergi Maturana,Manuel Bañobre,Quinten Vermeulen,Javier Perez M.,Frederick Zufelt,Brendan Lichtenberg,Arjan de Vreede,Sebahattin Atalar,Raphael Lauber,Christian Rose2,Paul Huber,Simon Etheridge,Lawrence Phipps2,Javier Isiegas,Tye Macleod,Lee Jenner,Fran Lucas,Lukas Winter,Ryan Bird,Joel Stampfli,Zachary Sober,Robert McNeal,Brad Teske,';
$promazda_drivers = 'Joshua S Lee,Craig Shepherd,Derek Hartford,Kevin Vogel,Eneric Andre,Alex Bosl,John Dubets,Jimmy Duncan,Albert Gisbert Falgueras,Sam Rosamond,Elliott Skeer,Robert Ulff,Oliver Patock,Robert Draper,Bruce Granheim,Mike Medley,Yusef Rayyan,John Szpyt,Nash Fry2,Barry Arends,Collin van Raam,Carl Modoff,Chris Meeth,Anver Larson,Alex Millward,Craig Evanson,Gulas Mate,Claes Poulsen,Tomasz Kordowicz,Maurice Gomillion,Brett Gardner,James Osborne4,Sepp Odoerfer,Kevin Hollinger,Craig Forsythe2,John Ellison,David Bessa Dias,Benjamin Cox,Rebelo Romain,Jeroen van Wissen,Arjan de Vreed,Gary Krichbaum2,Jonathan Heimbach,Nicholas Millard,Jake Hewlett,Alain Stoffels,Attila Papp Jr,Eddy Andersson2,Gabriel Garcia Olivares,James Strobel II,Kevin Giménez,Thibault CAZAUBON7,Derek Adams,Joris Valentin2,Robert Siegmund,Hildebrando Pinho Junior,Michael D Myers,Ben Clayden,Rob Donoghue,Elias Viejo,John Signore,Davis Rochester,Alexandre Gramaxo Gouveia,Jacob Bieser,Susan Blackledge,Robert B Eriksson,Alexandre Gravouille,Cody Siegel,Jan Spamers,Richard Sudduth,Szilard Halaszi,Shaun Barrowcliffe,Anton Kusmenko,Pierre Verne,Ryan Schartau,Florian Bayle,Vincent Hamet,Guillaume Dupont2,Imre Lukacs,Romain Pelissier,Xisco Fernández,Loris Amadio,Benjamín Carreiras,Andre Monteiro2,Robert Long2,Braden Graham,Duncan Watt,Paul J Ulliott,Tom Berendsen,David Strickland,Antonio Bermúdez,Scott Nicholson Jr,Adolf Egli,Sergi Maturana,Manuel Bañobre,Quinten Vermeulen,Javier Perez M.,Frederick Zufelt,Brendan Lichtenberg,Arjan de Vreede,Sebahattin Atalar,Raphael Lauber,Christian Rose2,Paul Huber,Simon Etheridge,Lawrence Phipps2,Javier Isiegas,Tye Macleod,Lee Jenner,Fran Lucas,Lukas Winter,Ryan Bird,Joel Stampfli,Zachary Sober,Robert McNeal,Brad Teske,';
$me = 'Ryan Hellyer,';
$phoenix_dallara_dash_drivers = 'William Swenson,Donnie Sanders,Liam Quinn,Steven Freiburghaus,Kurtis Mathewson,Joao Valverde,Bryant Ward,Tarmo Leola,Michael Kildevaeld,Garrett Cook,Hartmut Wagner,Alejandro Leiro,Frank Bieser,David Keys,Balazs Floszmann2,Maxime Potar,Aymerick Vienne,Jason Lowe4,Kevin Shannon,Henry Eric,Patrice Lebrun,Rémi Picot,Timothy Scanlan,Philippe Tortue,Antoine Gobron,Neil Thompson,Pascal Bidegare,Mathieu NEU,Kirk Smith,Laszlo Miskolczi,Sam Cook4,Jim Gibbs,Dale Robertson,Kelly Thomas,Adolfo Macher,Denis Nestor Kieling Kieling,Maik Lara Guerra,Richard Grimley,Herve Lanoy,Jesus Fraile Hernandez,Justin Adakonis,Matthew Carter7,Art Seeger,Martin Vaughan,Rob Collister,Jake Johannsen,Tyler Rahman,Jan Schumacher2,Esa Hietanen,Vahe Der Gharapetian,Jake Conway,Fernand Frankignoul,Daniel Förster,Joel Taylor,Marti Olle,Patrick Weick,Thierry Schmitt2,Rodney Bushey,Michel Rugenbrink,Zack Tusing,Trever Halverson,Peter Labar2,Steven Roberts4,Dirk Rommeswinkel,Daniel Redlich,Brian Spotts,Isaac Jaen,GÃ©rard AMBIBARD,Pablo Perez Companc,Robert Queen,Dewey Perry,Joshua Halvey,Charles Hinkle,Helio Santos,Austin Collings,Adam Smith5,Austin Eder,Joshua Baird,Harry Floyd,Joseph Scatchell,Dardo Nosti,Mario Alvarez,Michael Erian,Colin Earl,Kenneth Webb,Dave Dawson,Paulius Dunauskas,Trevin Dula,';
$formula_renault_last_2 = 'José Cantharino,Michael H Burton,Tae Kim,Mauricio Moreno2,Rob McGinnis,Chester Thompson,Pavel Philippov,Tony Vasseur2,Phil Letchford,Michelle Smeers,Jesus Manuel,Greg Garis,Jack Freke,Daniel Nyman,Dan Mocanu,Paul Nopper,Walter Bornack Jr,Charlie Rage,Augusto Henriques,Marco Van Wisse2,Saúl Quintino Santana,Vittorio Saltalamacchia,Rene Maurer,David Rojo Lopez,Cameron Dance,Kris Roberts,David VELASQUEZ,Viktor Shubenkov,Alexander Stock,Jerome Haag,Matt Bobertz,Fabio Aoki,Admir Nevesinjac,Stefano Calascibetta,Christopher Gray3,Marc Oros,Don Bowden,Andy R Moore,Roger Prikken,Naiche Barcelos,Daniel Berry,Fredrik Kvarme,Sergi Viñolas Font,Paul R Lewis,Marcos Silveira2,Curtis Martin2,Adam Leff,David Hoffmann,adam Saunders2,Ranford S Brown,Matt Cox,Salva quadradas,Terry Taylor,Sergi Cardó Catalan,Chris Homann,Reid Harker2,Andy Bonar,Tony Matthews,Artur Gozdzik,Manuel Heuer,Joonas O Vastamäki,David Waldoch,Lubomir Moric,Egoitz Elorz,Daniel Barrero,Scott Clarke,Gerald Zindler,Sandro Biccari,Rogier Visser2,Kouji Itoh,Takuya Sekimoto,Antonio Ortiz Poveda,Alexis Mattaruco,Francesco Tonin,Philippe Silva,Thomas Glad,Scott McClintock,Thiago Spencer,Damon Martinez,Jeremy Skinner,Tim Delisle,Jeroen De Quartel,Chris Burgess,Jerome Lapassouze,Jonas Toigo de Souza,Andrea Brachetti,Rogerio Schiavon,Marcio Marodin,Kristof De Busser,Adam Frazier,Antony Perryman,Angus Waddell,Tim McIver,Nikolay Andreev,Jorge Rolandi,Jorge D Romero,Dave Martin,Kevin Fennell,Sebastien Kendzierski,Stuart John,Thomas Scheuring,Lopez Alexandre,Angel Garrido Orozco,Bruno Domiter,Dimitri Coulon,Alejandro Blanco,Carles Bayot,Hannes Wernig,Bruce Keys,Marc Leyes,Ronnie Olsen,Caroline Viscaal,Janneau Rompelberg,Dennis Gerressen,David Buitelaar,Jakub Jezierski,Sakiran Partowidjojo,Domenic Guras,Daniel Gazquez,Christian C Meyer,Raul Alfonso Valero Llordes,Fabrizio Donoso,Peter Berryman,Frank van Brandwijk,Phil Reid,Claudio Candio,Martin Donati,Charles Stark,Anibal Colapinto,Teemu Kotila,Adam Tierney,David Porcelli,Patrick Wartenberg,Carlos Cabral,Marcos Marcelo,Claudio Costarelli,Alex Düttmann,Bruce Poole,SeongWon Jeong,Ossi Varjoranta,Steven Campbell,Mario Ledesma,Ken Daly2,Joseph Nelson2,Paul Trgovac,Daniel Cunha Prado,Miroslav Ju?í?ek,Aaron Turner,Pedro Henrique Moisés,Frederic Guillaume,Edward A Samborski,Derwyn Costinak,Jose Alberto Hidalgo Nicolas,Brent Mills,Cuba Gonzalez,Alberto Pérez,Evan RT Imray,Giancarlo Bartolini,Jack Hobbs,fabrice BAZIN,Johan Hellemans,Robert Dilbeck,Davide Giovanni Solbiati,Thomas Mrazik,Alejandro Alvarez3,Sam Satchwell,Patrick Luzius,Oscar Quiroga,Richard Schouten,Ralph Thomson,Alex Stumpe,Anto Mn,Erkka Lindström,Renaud Soudiere,David Jarvis,Victor Martins,Christian Schultz,David Sanchez5,Daniel Clarke,Jose Pleguezuelos Langa,Stéphane LEMAIRE,Edmundas Azlauskas,Hiroshi Sakai3,Marco Cesana,Jari Viinamäki,Mickel Francis,Josh Slade,David Thompson12,Kurt Bagnell,Dylan Prostler,Sergio Pasian,Jose Telmo,Elliot Leach,Scott Lefeber,Alan Hix,Marc Martinez Forner,Jeremy Lapainis,';
$random_hosted_race_girls = 'Jennifer Kind,Lauralee Blackburn,Charlene Swinehart2,Ronda Boxler,';
$spa_indycar_drivers = 'Rados?aw Sitarz,Henry Bennett,Arturo Amro,Carlos López,Hercules L. Santos,Carlos Washington,Pebst Augusta,Antenor Junior2,Lucas Stattel,Chris Langswager,Mauricio Moreno2,Rob Unglenieks,Nelio Cunha,Vinicius Marega,John Ahles,Bart van Velzen,Jared Dziedzic,Brian Beers,Radek Sykora,Tae Kim,David Adams8,Ralf Schmitt,Lance Simon,Patrick Moose,Martin Kober,Stanley Sullivan,Andreas Eik,Domingos Frias,Albert López,Carl Barrick,Gerald Zindler,Enrique Tramontin,Jeffrey howard2,Rick Hundorfean,Gary Borkenhagen,Thiago Bello,Tyson Sailer,Krysta Nelson,John Erickson,Donnie Shealy2,Andy Rhodes,Alejandro del Campo,Kurtis Mathewson,Simon Briant2,Jackson Patricio Dutra,Andre Moreira,Bill Gallacher Jr,Ronald P. Brent,Damon Martinez,Christian Steele,Austin Espitee,Andrey Efimenko,James E Davis,Andrew Burns2,Rikki Gerhardt,Richard Kaz,Henry White,Don Jareño Villarreal,John Downing,Andrew Kinsella,Joe Flanagan,David Wall Jr,Kim Short,Nolan Baltz,';
$randoms = 'Bryant Ward,Kevin T Firlein,Lisa Ryan,Henry Bennett,Austin Espitee,Richard Tam,Andrey Efimenko,Martin Kober,Sergio Morresi,Pebst Augusta,Bill Gallacher Jr,Carlos López,Jeffrey Oakley,Carl Barrick,Vinicius Marega,Kenneth Bafford,Travis MacChesney,Thomas Connolly,Isaiah Locklear,Michael Elsom,Patrick Collins,Garrett Cook,Gail Walker,Philip Moore3,John Macchione,Richard Dempsey4,John Chapman4,Austin Johnson6,';
$girls1 = 'Stephanie Lessentine,Laura Lawson,Jani Penttinen,Peg Mulrooney,Quinn Johnston,Courtney Terrell,Lauri Salo,Jessyka Vox,Andrea Guglielmetti,Jani Järvinen,Alysson Pereira,Jone Kaijanen,Tommi Talonen,Angel Ledesma,Christine Marie Tillmann,Andrea Baldi,Avery McDonald,Marie Hruschka,Ellis Teal,Oakley Higham,Karlyn Lyneis,Angel Fernandez Cobo,Elaine Krizenesky2,Leila Wilson,Lauri Happonen,Emily Jones,Terrie Blackburn,Monica Clara Brand,Angelika Pavlowski,Yanick Compostel,Rebecca Pauline,Courtenay Smith,Bas Slob,Briar LaPradd,Hena Hakkanen,Laureen Woods,Kalle Ruokola,Kelly B. Crabtree,Erin Nagy,Jordyn Charge,Kendall Frey,Jade Williams,Andrea Girella,Jady Baumgardner,Sasha Todorovic,Alysson Pacheco2,Niki Suhonen,Elizebeth Muscat,Kendall A Nicholson,Lauri Linna,Tommi Vacklin,Kenzy Nieuwhof,Anneli Jakobsoo,Andrea Gatti,Mackenzie  Korince,Sarah Laprevotte,Saby Hanyik,Jani Koskinen,Daniella Blanco,Kari Nyman,Ariel Acastello,Kelly Newman,Kelly Wilson,Sasha Milosavljevic,Rylan Furler,Madison Casey,Jann Dircks,Skyler Grissom,Lauri Ketola,Vail Riches,Jani Polameri,Daniela Azunaga,Deborah Soete,Kelly Niquette,Valerie Cote,Elena Gomez,Yelena Medwedewa,Laura Bond,Kendall Shaw,Nicole Johnson,Bas Neijenhof,Kaitlyn McDade,Jennifer King,Matti Höylä,Tamy Accioly,Jessie Rougeux,Andrea Stefanina,Ariel Eduardo Bernardi,Andrea Giachè,Payton Weakley,Angel Dean,Sky Willis,Jannis Koopmann,Jocelyn  St-Martin,Danni Fugl,Kalle Valli,Christel van Essen,Kelly Kozek2,Angel Moreno Garcia,Julie Redmon,Andrea Disarò,Sandra Castrogiovanni,Jani Kattilakoski,Tomi Mäkinen,Sarah Souders,Kari Ikonen,Lauri Hiljanen,Andrea Boccellari,Lisa Ryan,Laura Perry,Kenley Brown,Lauren Hoock,Brynn Pearce,Jade Buford,Andrea L Bozzer,Ariel Alaniz,Magalie Damermant,Floris de Wit,Jani Rautio,Raffaella Cucciarre,Andrea Scognamillo,Jani Pitkonen,Kelly Sprayberry,Ariel Doman,Jani Smolander,Sasha Anis,Andrea Lojelo,Yury Korn,Kelly Dahl,Vivian Santiago,Amoreena Hall,Molly Steinberg,Tomi Rostén,Angel Hernandez,Tommi Saunter-Chun,Janis Vigulis,Ingrid Marti,Niki Loipold,Kalle Peltonen,Andrea Corsetti,Matti Huotari,Matti Klami,Matti Koskinen,Kari Kalen,Klaudia Monica,Tommi Nieminen,Amber Laybourne,Elis Jackson,Lindsay Barrie,Julia Pros Albert,Ariel Hartung,Hannah Lewis,Jone Randa,Kelsey Roach,Andrea Fraccalvieri,Angel Pina,Reese Starowesky,Tomi Virtanen,Gillian Woods,Kimber Jansen,Skylar Dunning,Stefanie Fleper,Angel Felipe Nuño Garcia,Bas Westerik,Tomi Salmi,Lauri Haverinen,Cherie Mollohan,Conny Naslund,Remy Lozza,Remy Provencher,Floris Bieshaar,Kalle Vaimala,Marjan Koderman,Tomi Hannuksela,Jani Kankaanpää2,Andrea Frollo,Sage Karam,Sara Dove,Angel Broceno,Denise Hallion2,Ellen VanNest2,Anne Struijk,Angel Lozano,Sian Walters,Kari Kiviluoto,Alyssa Ferrie,Tomi Mairue,Gwen Kolsteren,Dalila Magdu,Andrea Stefanoni,Carmen Comeau,Andrea Del Piccolo,Nelli Pietro,Jennifer Grifhorst,Lari Niskala,Pier Andrea Cappelletti,Tommi Seppänen,Sabrina Gramß,Laura Zampa,Angel Rosa2,Susi Badia,Chandra Wahyudi Jong,Jocelyn Lauzière,Sasha Ebel,Rus Marius,Lauri Mattila,Andrea Masiero,Andreia Azevedo,Sena Pugh,Kari Koski,Montserrat Cervera,Kelly Knight,Ally Sinclair,Lisette Davey,Matti Mäki,Bas Metselaar,Renie Silveira Marquet Filho,Kaya Selcuk,Niki Djakovic,Matti Räty,Tomi Ojala,Jani Petäjäniemi,Sindy Lajoie,Alison Willian Gomes Dos Santos,Niki Kresse,Agnes B Kaiser,Shelby Blackstock,Leila Hammadouche,Kari V. Saastamoinen,Dian Kostadinov,Tommi Piekäinen,Lindsay Wauchop,Reiko Arnold,Myung Kun Shin,Sofia Stella,Kari Ontero,Pascale Paquet,Joyce Martin,Holly Crilly,Sunday Awoniyi,Andrea Melonari,Jani Nurminen,Maris Sulcs,Jessica Dube2,Andrea Lo Presti Costantino,Delaney Mulholland,Amal Youness,Tomi Leino,Andrea Donati,Bas Rowinkel,Kalifa Dong,Jonni Molyneux,Jani Vähäsöyrinki,Hedwig Gager,Tommi Ojala,Lark Mint,Ilham Halabi,Ronni Brian,Kalle Lehtonen,Kalle Uusitalo,Nathalie Gaubert,Andrea Rizzoli,Angel Saez Juan,Andrea Zecchetti,Georgi Dimov,Andrea Schilirò,Angel Banegas,Niki Jessen,Jani Mikkola,Angel Hernandez Corte,Kimm Johansson,Vivian Santiago,Amoreena Hall,Molly Steinberg,Ingrid Marti,Amber Laybourne,Julia Pros Albert,Hannah Lewis,Gillian Woods,Kimber Jansen,Skylar Dunning,Stefanie Fleper,Lauri Haverinen,Cherie Mollohan,Conny Naslund,Sara Dove,Denise Hallion2,Ellen VanNest2,Anne Struijk,Sian Walters,Alyssa Ferrie,Gwen Kolsteren,Nelli Pietro,Jennifer Grifhorst,Sabrina Gramß,Laura Zampa,Susi Badia,Jocelyn Lauzière,Sasha Ebel,Lauri Mattila,Ally Sinclair,Lisette Davey,Sindy Lajoie,Alison Willian Gomes Dos Santos,Agnes B Kaiser,Leila Hammadouche,Sofia Stella,Joyce Martin,Holly Crilly,Jessica Dube2,Nathalie Gaubert,Kimm Johansson,';
$skip_barber = 'Jaime Simonet,Andrew Procario,Edwin Vallarino,Francois Boulianne,Miguel Garcia sanchez,Isaac Silva Gonzalez,Gunnar Moller,Carlos Ventura,Richard Martinez de Morentin Suescun2,Thomas Merrill,Alex Baciu,David Miller10,Brad Henry,Muhammed Sahan,Alan Gomez Selfa,Chris Malcolm,Matt Malone,Stephan Bloechlinger,Adrian Garcia Cruz,Xavier Bertrant,Michael Morrison2,Kane Halliburton,Sébastien PETIT,Mika Johannes Kovanen,Andrew Horsley,Angelo Abarca,Charles Anti,Gordon Ramsay,Richard Warmingham,Romain Marchal,J. Félix Díaz,Jesus Menendez,Adam Rylance,Andre Castro,Manuel Valente,Nahum Solà,Llyr Hughes,Bruno Barbugli,Jack Turner,Wolfram Fiedler,Lucas Loyarte,Tore LÃ¸kken,Richard Bagshaw,Giovani Diaz,Fernando C Rodriguez,Michael Baley,Daniel Cabanillas,Don Yuhas,Steven Patmore,Daniel Behrensen,Roman Cajka,Karthik Pai,Neil Bontems,Derek Issa,Peter Cnudde,Jonathon Sheen,Tyler Tucker,Álex Ardisana,Mario Gil,Bryan Krauss,José Manuel Esteban,Michel Cozzone,Sergio R Garcia,Ruben Dominguez Prieto,Agustin Martos,Juan Carlos Marquijano,James Flannery,Gavin Newcombe,Thiago Canola,Iban Etxeberria,Tibor Sandor,Alexander Lauritzen,Julian Bell,Mark Brightman,Rubén Fernández Muñiz,Dylan Francis,Dave Chv Taylor,William Hartman2,Matthew Woollett,Elvis Allende,Oliver Fuentes,Bob Kern,Tyler Hervias,Alberto Pellegrini,Garth Galinat,Dave Boyle,Federico Leo2,';
$skip_barber2 = 'Greg Seitz,Johnny Guindi,Jaume Dalmases Torres,Tuan Tran,Justin Tipton,Mike McCormick,Iriome martin,Tim Beaudet,Jarrod Marks,Vicente Gascon Marti,Fahim Antoniades,Julien Lardy,John Ehlers,Binesh Lad,Steve Owens,Johan Lespinasse,Michael Guariglia,Dave R Roberts,Santiago Rodriguez,Federico Montini,Mark Chorley,Sara Savage,Sean Dittmer,Edward Torres,Craig Foster,Paul Spencer3,Andrew Bolton,Diego Palomar,Sean Michael Jr.,Tomas Marcos,Jaime Fierro,Harley Lewis,Tom Ward,Ivan Barreira,Jimmy Anthony,Brenden Campbell,Carlos Serantes,Kevin McKnight,Oscar Tolnay,Fernando Vega,Daniel Ackland,Anthony Peeble,Steven Brumfield,Joao Oliveira,Eric Schulhof,Steven Wareham,Dave Price,Robson Cardoso,David Kerouanton,David Workman,Alberto Veiga Grana,Maxime de Tilly Brisebois,Zoltan Herczeg,J william Smith,L Leroy Coppedge,Michael Heuschele,Adailton Santana,Nick Ritcey,Pete Tinkler,Andrew Love,David C Payne,Mike Devereaux,Curtis Thomason,Jim Brewster,Jon Sully,Israel Perez,Alberto Medina,Patric Fairbanks,Benn Williams,Xavier Baboulaz,Nicholas Vaughan-Roberts,Aitor Lejarraga,Jochen Schächtele,Alexander Honing,Santiago Cabrera,Daniele Forgiarini,David Bragg,Racim Fezoui,Emilio Romero,';
$skip_barber3 = 'Dennis Johansen,John J Kern,Bryan Sutton2,Rick Tarvin,Daniel Rivito,Luiz Gonzaga Filho,Vivek Reddy,Patrik Flis,Ryan G Walker,Ricardo J Faria,Eduardo Prado,Rafael Torres Castaño,Jackson Neesley,Xavier Solà,Stef Veenhof,Ivan Montoya,Simon Edwards,Kimmo Vierimaa,Stephen Bair,Angelo Cellura,Timothy Roman,Bicor Valencia,Jesus Martinez Hort,Cédric Gesmier,Peter Lo,Jean-Francois Boscus,Janne Vaarasuo,Mihail Latyshov,Mario Civera,Jesús Montiel,Gerard Florissen,Gregory Thompson,Ville Poutiainen,Ric Davis,Jukka Korvenranta,Jaime Ballesteros,Hiroshi Nobuoka,Daryl Ridley,Vicent Roig,Bart Martinovic,Graham Marshall,Ian Haycox,Miguel Aguilar,Matthew Woodley,Jhonathan Selas Sanchez,Johnny Voegeler,Michael Parsons2,David Sitler,Richard Franco,Hector Soler Font,Francesc-Xavier Casado,Arnaud Chambefort,Cyril Broeders,Gary Thomas,Steffen Bremer,Kerem Avan,Felipe Nuño,Samuel Soto,Tomek Weber,Andreas Arvanitantonis,Antonio Jesús Villalón,Iñigo Bea,Ismael Habib Jiménez,Michael Rattigan,Sergio Fernandez,M.A. Cabo,Manel Segret,Pascal Van den Hoek,Ivan Mula Vivero,Joeri Cox,Martin Turner,Yuuto Fukuchi2,Nicklas Gjerulff,David Arnold,Herve Boyard,Marc Gammack,Ismael Pereira,Christopher Kasch,Ashley Higham,Jos Smets,Bjoern Reddehase,Christopher Zoechling,Brice Michelon,Barry West,Christian Meese,Endre Papp,Alex McKellar,Cooper Webster,Paul Rooks,Phillip Worley,Eric Violett,Sergio Aldaz,Jonatas Silva da Costa,Kevin Huang,Luke May,Bruno Gallego,Alexis Demers,Shinji Kito,Jason Pitts,Andrew B Whitehead,Johannes Kok2,Philip Robertson,Ozgur Basboyuk,Costa Andrea,Evaristo Gonzalez Ruiz,Graham Forrest,Raul Sanz,Richard Yalland,Antonio Rita,Carlos Rodriguez Martin,Ricardo Gama,Gustavo Vallerio Mundici,Oscar Vall Gallén,Ryan Williams3,Matthieu Fourtemberg,Nick Carty,Nathan Brown6,Johan Poggi,Junior Yearwood,Steffen Seljeskog,Simón Durán Toledo,Vincent Lemarchand,Thomas Zieger,Alejandro Carrasco,Kheireddine Bouafia,Bob Batalla,pascal Metselaar,Fernando Casaus,Joe Cole,Roberto Martin,JB Massida,Bastien Remise,Christian Nilsen,Jordi Moretó,Lewis Parin,Shigeru Ogawa,Cavan Taylor,Gareth Lavelle,Gary Wolboldt,Sam Dobie,Jeroen Ronsmans,Harry Fuchs,James Mckie,Alberto Segovia,Ashley Work,Michael Jones13,Jacob Jacobsen,Joseph Oetzell,Dean Malone,Alan Catale,Glenn Croswhite,Grant Bray,Andrea Ventura,Kent Connolly,Cody Bolinske,Rodrigo Capeleto Ferreira,Lucas Stewart,Austin Zetzman,Cristian Gavin Alvarez,Arnaldo Petcov,Manuel J. Lopez,Ron Borden Jr,FT Fenaux,Jesus Perez Paredes,Maikel Rodriguez Fuentes,Sergio Hidalgo,Alex Saunders,Jose Miguel Rosillo Nieto,Casper de Kort,Karl Hammarling,Neil Middleton,Mark J Turner,Oriol Espona,Marko Hyypia,Jesper Giortz-Behrens,Jens Paul,Ezequiel Beltramo,Kristofer Moreau,Christian Olsson,Alan Needle,Calvin Allison,Andy Baker,Robert Mason2,Marcelo Couto,Alberto Jiménez Sáenz,Jason Coetzee,Arne Stehn,David Peña Jimenez,Nico Roman,Michael Messenger,Viktor Nagy,Sami Sallinen,Robert Boaden,Craig Platts3,Ricardo Margarida,Mikael Engstroem,Juan Aragones,Motonori Handa,Chris Polley,Steven W Thomas,Pep Meyer,Ian Robertson,Miha Filej,Michael Fiedler,Luke Muir,Brion Sohn,Nim Cross Jr,Chris Tweney,Rafael Limon,Nick Read,Nixon Montero Ugalde,Peter Stubbins,Robin Luckey,John Battista,Matthew D Billings,Paul Godfrey,Matthew Murphy2,Joanmi Marzo Gil,Ray Ehlers,Zachary French,Brian Heiland,Austin Hervias,Diogo Francisco,Richard Doughty2,Edu Pacios,Mike Swenson,Aldis Polis,Javier Damlow,Michael Smith41,Emanuele L Mambretti,Mark Cossins,Fabian Menetrey,Damien Devaux,Edward Bewers,Dave Killens,Antonio Diaz Estevez,Antonio De Marchis,Samuel Reiman,David L Hicks,Victor Suarez Rivero,Jose Arbos,Ricardo P Silva,Charles-Eric Lemelin,Darren Lessue,Dexter Cutts,Alex J John,Pavel Suchacek2,David Strathern,Tim Berti,Chris Bland,Cory McLemore,Alberto Mangual,Jerry Schuryk,Jerad Sharp,Shawn Noble,Matthew Murphy4,Jeremiah McClintock,Wesley Winterink,Paul Barnett,Hunter Reeve,Jorge Reinoso,RJ Bishop,Scott Malcolm,Theodore Burns,Ben Glenton,Lorenzo Garcia Mira,Jan Hoffmann,Oscar Artiñano,Niels Clyde,Adrian Brzozowski,Marcel Seidel,Andreas Robertsson,Dave Gymer,Mario Nuñez Nieto,Mark Smith,Michael Sullivan,Adrian Vila,Ronald Rasmussen,Fabio Rühl,Tino Gosselin,Edward Tink,Jochen Büttgenbach,Stuart Bradley,Jason Warren2,Paul Nichols,Bastiaan Huisman,Joe Bradley,Sota Muto,Phillip Stoneman,Dan McGuirk,Kirk Lane,William L Smith,Duane Benzinger,Johannes Wellhöfer,Marcelo Pellegatti,Martin Cruz,Casey Black,Arturo Cruz Ramos,Ben Watkins,Ashley Beard,Kristo Chinmai,Håkon Grebstad,Peter Classon,Hans Heuer,Carlos Quilez,Anthony Catt,Alfredo Malo R,Guille Garcia,Tim Hendrixen,Robert Fox4,Borja Padilla Marrero,Mark Driessen,Gonzalo Camara,Mat Ishac,Barry Langford,Daniel Lezcano Manso,Graeme Hudson,Jason Tyler2,Masaki Tani,Luke Whitten2,Jacques Swanepoel,Mark Jeffery,Mario Visic,David Litzistorf,Mick Grey,Marcos Bodas5,Phil Lee,Gregory Doan,Kevin Henderson,Philippe Leybaert,Jose Portilla,Brian Leavitt,Steve Ficacci,Andreas Andersson3,Alex Clarke,Phillipp Urquhart,Ken Kurtz,Fran Gongora,Koldo González Gomez,Lando Norris,Kelvin van der Linde,Jeff Deliere,Teemu Vaskilampi,Oleg Melihov,Alex Kattoulas,Jon Scholtz,Daniel Edmonds,Alberto Cerda,Keith Sharp,Tim Huss Jr2,Wolfgang Wildenauer,Jonathan Chasteen,Todd Ingves,Raico Álvarez Feijoo,Hector Balaguer,Les Peck,Michael Morris6,Andrew Lawler,Carlos Kac,Craig Spitzer,Daniel Williams4,Robert Young,Ferran Serra Cercos,Martin Brandon,Denis Fricher,Carlos Arnau Ros,Steven Gatesman,Róbert Sebestyén,Mario Díaz,Juan Bleynat,David Harney,Claudio Monteiro2,Jesper Kosse,Jose Luis Lopez Abellan,Nick Sigley,Luis Hernandez Sanchez,Gabriel Farias,M.C. Visser,Charlie Kerschbaum,Andrew Brewer,Nicolaj Appelby Hansen,Harrison Finch,Yuuya Kimura,Stuwie White,Loic Villiger,David Goldaracena,Jan Aleksandrov,David Juez,Stephen Jenkins,Kyle Trudell,Paul Wood-Stotesbury,Michael Pianalto,Jimmy Boylan,Brent Wilson2,Doug Metzel,Marko Perich,John Oliver6,Jeremy Hobson,Matthew Shanks,Steve Kagerer,Alexandre Lorenzini Crespo,Bryan J Kelly,David Hickman,Tiago Pires,Zachary Buchanan,Mike Paschen,Bruno Lambrecht,Alberto Doñoro,Daniel Mesa Ramirez,Kenny Bairolle,Nick Oneill,Franck Levasseur,David Ballew2,Marcel van Bloppoel,John Hall3,Luis Leal,Charles T Jordan,Karl Handley,Armando Luque,Nick S Curry,Elijah Bautista,Christopher Lawson3,Oriol Moret Alcalde,Jared de Kruijff,Camille Younan,Christian Greule,Sergi Martinez,Vesa Jylhä-Ollila,Andrés Ramírez Pérez2,Alex Steenbruggen,Dave Cameron,Andrew Kerr3,Sanjin Delalic,Yusuke Nodake,Mark Kerr2,William Tahran,Craig Deshon,Jason Schiwy,Jonathan Tussey2,Johnson JW Yong,Nicolas Camacho,Connor Parise,Matt Koerner,Cooper Collier,Patrick Ramirez,Daniel Kaps,Rodrigo Meezs,Michael Keymont,Daniel Serrano Rayo,Jackson Freer,Joe Marlin,Christophe Fuchs,Chris Kierce,Scott Myers,David Garrido Sanchez,Tom Ruff,Carlos Andres Medrano,Sebastiaan Neefjes4,Marco Corti,Rod Dagneau,A Henderson,Aitor Figal Fernandez2,Jose Antonio Yañez Buron,Siim Loog,Anton Oud,Salvador Jimenez,Ben Clemson,Yuuya Tanaka,Aiden pyke,Arthur Rymer,Bruno Tassone,Nathan Dudek,Andrew Trimbach,Jose Luis Paz Vilas,Jacob Smith2,Adam Facciponti,Daniel Nogueras,Jack Laidlaw,Jef De Haes,Kai Uwe Gerlach,Javier Alvado Moll,Matt Fretwell,Taiki Yamaguchi3,Gregory E White,Jose Conde Ortega,Steve Smyth,Adolfo Martinez Vazquez2,Sam Dunstall,Rick Hansen2,Ivan Prendes,Karl Thomas Daum,Alto Dykes,Alex Ward,John Flowers,Xavier González,Gerald Chevalier,';
$formula_renault2 = 'Aleksandr Potapov,Colin Gregory,Robert Jones10,Alex J Krejcie,Ryan Arroyo,Samuel Steffy,Carlos Fonseca,Antoine Thisdale,Diidier Lapchin,Alistair Hay,Ivan Hernandez,Jiri Mojak,Stephan Roesgen,JC Tussey,Jean-Francois Godin,Alex Thornton,Miguel Invernon de Julian,Thomas Ligon,Ryan Ligon,Lee JR Williams,Christoph Aymon,Matthew Wilson4,Abhinav Thakur,Tom Depke,Steffen Herrmann,Claude Lessard,Chris Knight,Harry Cowan,Benjamin Morse2,Daniel Felix,Tobias Beckmann,Ziv Sade,Rene Lopez,Rian Moore,Scott McIntyre,Frank Rask,Lionel Gamet,Kristian Takacs,Samuli Strang,Dmitri Janis,DariuszD Szymecki,Fabrizio Raffa,Roel Frissen,Renat Satdarov,Jacques Richard,Wei Han Chan,Jeronimo Mosquera,Jeff Sparks,Duane McCarthy,Steve Honan,Gabriel Borghi,Isaac García Domínguez,Alex Fedurco,Mark Flowers,Greg Waters,Geoff Killick,Bart Vandenryt,David Araque,Marcus Simonsson,Lucas Navarrete,Tristan Koch,Scott Garner,Nick Kallinosis,Koichi Wakiyama,Adam Perkins,Michael Hilliard,Thomas Jordan,Ronny Granzow,Shayne Allen,Tyson Meier,Jason Lundy,Aecio Telles,John Gomes,Sebastian Peter Schaar,Alexander Porcelli,Roger Jackman,Michael Knapp,Tyler Lugo-Vickery,Alexander Przewozny,Aleksandr Drozdov,Brenden Baker,Marcin Gadzinski,Oliver Augst,Christophe Brengard,Marc Schulz,Anthony Burroughs,Marty Vrana,Kevin R Jowett,Julien Dauber,Miguel Cela,Jenno Buelders,Jose Gomez2,Robert Fletcher2,Dean Oppermann,Thomas Ingram,Gary Weaver,Joshua Stuart,Aaron Upp2,Daniel martin leite,Jason Walat,James Webb6,I.M Keeman,Liam Gordon,Josh Hall,Mitsuhiro Kozai3,Christian Van Egmond,Davy Schaepdryver,Gavin Simpson,Jonathan Leighton,Darius Trinka,Jose Carlos Campodonico,Daniel Willits,Jonathan Perl-Garrido,Xhulio Zhonga3,';
$indycar2 = 'Adam Blocker,Cameron MacPherson,Andre Carlos Smith de Vasconcellos,Julien Flouret,Riku Roiha,Cesare DiEmidio,Matt Miller,Robert Obrohta,Randy Shewmake,Anthony Obrohta,Robert Crouch,Joel Tremblay,Tamas Rekettyei,Mark Bartholomew,Fabian Kloth,Roman Belogorodov,Markus Niskanen,Cristiano Benevenuto,Henrik W Nielsen,Cristian Camozzi,Toni Andrade,Mark Prince,Rafael Davila,kiko Dominguez,Daniel Vieites,guy TESSORE,Adam Webb2,Craig Hobson,Ryan P Andrews,Sean Disbro2,Justin Morton,Brandon Kilgour,Ilya Babansky,Zachary Sears,Jeffrey Oakley,Philippe Lambert,Robert Lewis3,Michael Peters,Sergio Madrid Hernandez,Markus Kuttelwascher,TM Hauser,Joshua Chin,Daniele Noventa,Roy Ricklin,Paul Morris,Brinton Hockenberry,Brian Cross,Richard McClure,Doug Lierle,Victor Diprizio,Brian Zager,Irmas Ibric,Robert Collins,Evan Black,Josh Lewis,Alessandro Dalledonne,';


// ********** NOT SENT YET ***********
// Women to add - need to hunt through for male names before sending
//Laura Lawson,Lauri Salo,Angel Ledesma,Christine Marie Tillmann,Avery McDonald,Ellis Teal,Angel Fernandez Cobo,Leila Wilson,Lauri Happonen,Emily Jones,Erin Nagy,Sasha Todorovic,Lauri Linna,Sarah Laprevotte,Ariel Acastello,Sasha Milosavljevic,Skyler Grissom,Lauri Ketola,Daniela Azunaga,Laura Bond,Jennifer King,Jocelyn  St-Martin,Julie Redmon,Sandra Castrogiovanni,Sarah Souders,Lauri Hiljanen,Laura Perry,Sasha Anis,Vivian Santiago,Molly Steinberg,Ingrid Marti,Klaudia Monica,Amber Laybourne,Julia Pros Albert,Ariel Hartung,Hannah Lewis,Gillian Woods,Kimber Jansen,Skylar Dunning,Stefanie Fleper,Lauri Haverinen,Cherie Mollohan,Conny Naslund,Sara Dove,Denise Hallion2,Ellen VanNest2,Anne Struijk,Angel Lozano,Sian Walters,Alyssa Ferrie,Gwen Kolsteren,Carmen Comeau,Nelli Pietro,Jennifer Grifhorst,Sabrina Gramß,Laura Zampa,Michelle Smeers,Susi Badia,Jocelyn Lauzière,Lauri Mattila,Ally Sinclair,Agnes B Kaiser,Leila Hammadouche,Sofia Stella,Joyce Martin,Holly Crilly,Jessica Dube2,Susan Blackledge,Mary Ford,Jenny Balzer,Laura Perez Garcia,Lauryn Brown,Jasmin Dizdarević,Wendy Crozier,Jocelyn Prévost2,Skyler Rivera2,Jennifer Denis,Daniela Taubert,Heidi Ammari,Alison Hine,Skyler Sisson,Alison Marshall,Mary Goins,Ellinor Ström,Lauri Kongas,Vivien Caplat,Wendy Sarrett,Ana Laforgue,Svetlana Dorokhova,Patricia Pasciolla,Julie Robertson,Alicia Avendaño Marín,Jennifer Barroso Ledo,Lynette Markham,Kayleen Hoy,Sandra Palm,Whitney Strickland,Kristen Barra,Valery Vodchits,Shawna Shade,Leanne Cordon,Katja Wolf,Camilla Deri,Sara Savage,Anne Medema,Jocelyn Mouton,Lauri Mäntyvaara2,Jennifer McDonald,Sara Black,Abby Harris,Maria Hollweck,Chelsea Angelo,Barreda Calvo,Lauri Koskinen,Angel Roglan Beltri,Angela R Coleman,Nadine Sander,Lana Opačak,Erin Miller,Rathana Danh Sang2,Maja Ljunggren,Georgi Raychev,Lauri Perlström,Elodie Hannequin,Saskia Schmidt,Chrystle Jones,Adria Serratosa,Remy Ammour,Gila Dezso,Vesna Paternoster,Kristina Matagić,Lucia Esposito,Kendall Baumann,Alin Albulescu,Emily Coates,Angel L. Lahoz,Christine Valencia,Celia Sousa,Jocelyn Pellé,Jennifer Heintzschel,Ruba Oliva,Vanesa Beatriz Martinez,Jessie Eduardo Hemink2,Miki Voj,Flo Kremser,Sarah Toplis,Oakley Peterson,Angel Bajo,Camille Caytan,Angel L Rodriguez Alcalde,Indra Feryanto,Rosa Maria Iglesias Diaz,Sabrina Enting,Ariel Varro,Lauri Lään,Anne de Vries,Remy Thendrup,Emili Mulet,Addy Wood,Camille Spillman,Fiona Binney,Maria Bako,Jannis van der Eyck,Jessie Lan,Kendall Davis,Maria Stuart2,Jocelyn Brassaud,Galina Bruksh,Conny Lundell,Inge Hansesætre,Paw Schou,Andi Sentkowski,Estefania Rubio Santiago,Jannis Mueller,Anne Schwendiman,Jennifer Parks2,Janis Balodis,Linde Johnson,Dian Dechev,Yanick Aubin,Jocelyn Bellemare,Jojo Ak,Janis Braslins,Leen Melissant,Edwina Van der haegen3,Angel Huerta Gomez,366


$contacted = $current_signups . $personal_contacted . $indycar_road_drivers . $laguna_seca_dallara_dash_drivers . $promazda_drivers . $phoenix_dallara_dash_drivers . $me . $formula_renault_last_2 . $random_hosted_race_girls . $spa_indycar_drivers . $girls1 . $skip_barber.$skip_barber2.$skip_barber3.$formula_renault2.$indycar2;


$contacts = '';
foreach ( array_merge(
	explode( ',', $contacts ),
	explode( ',', $contacted )
) as $x => $driver_name ) {
	$personal_contacts[$driver_name] = true;
}



$events = array(
/*
	'indycar2' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
*/
	'formula-renault-2' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'indycar-spa' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'skip-barber' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
/*
	'indycar' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'laguna-seca' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 75,
		'time_2'           => 76,
		'time_3'           => 77,
	),
	'phoenix' => array(
		'incident_ratio_1' => 0.6,
		'incident_ratio_2' => 0.8,
		'incident_ratio_3' => 0.9,
		'time_1'           => 20.65, // Times largely irrelevant as qual set to 0
		'time_2'           => 20.7, // Times largely irrelevant as qual set to 0
		'time_3'           => 20.75, // Times largely irrelevant as qual set to 0
	),
	'sebring-promazda' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'bathurst-promazda' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'spa-promazda' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'formula-renault-last-2' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	*/
);

foreach ( $events as $event => $vars ) {

	$incident_ratio_1 = $vars['incident_ratio_1'];
	$incident_ratio_2 = $vars['incident_ratio_2'];
	$incident_ratio_3 = $vars['incident_ratio_3'];
	$time_1 = $vars['time_1'];
	$time_2 = $vars['time_2'];
	$time_3 = $vars['time_3'];

	$directory = get_template_directory() . '/tools/results/' . $event . '/';


	// Get iRacing stats
	$dir = wp_upload_dir();
	$stats = file_get_contents( $dir['basedir'] . '/iracing-members.json' );
	$stats = json_decode( $stats, true );

	$csv_files = scandir( $directory, 1 );

	foreach ( $csv_files as $key => $csv_file_name ) {

		if ( '.csv' !== substr( $csv_file_name, -4 ) ) {
			continue;
		}

		// Get CSV data
		$csv_file_path = $directory . $csv_file_name;
		$csv_file_content = file_get_contents( $csv_file_path );
		$csv_file_content = str_replace( '"', '', $csv_file_content );
		$csv_file_rows = explode( "\n", $csv_file_content );

//print_r( explode( ',', $csv_file_rows[3] ) );die;

		// Stripping description out
		unset( $csv_file_rows[0] );
		unset( $csv_file_rows[1] );
		unset( $csv_file_rows[2] );
		unset( $csv_file_rows[3] );

		foreach ( $csv_file_rows as $key => $row ) {
			$cells = explode( ',', $row );

			// Get name
			if ( ! isset( $cells[7] ) ) {
				continue;
			}
			$driver_name = $cells[7];
			$driver_name = utf8_encode( $driver_name );

			// Ignore personal contacts
			if ( isset( $personal_contacts[$driver_name] ) ) {
				$drivers[$driver_name] = 'personal';
				continue;
			}

			// Get qual time
			$qual_time = explode( ':', $cells[14] );
			if ( isset( $qual_time[1] ) ) {
				$qual_time = ( $qual_time[0] * 60 ) + $qual_time[1];
			} else {
				$qual_time = 0;
			}

			// Get qual time
			$fastest_lap_time = explode( ':', $cells[16] );
			if ( isset( $fastest_lap_time[1] ) ) {
				$fastest_lap_time = ( $fastest_lap_time[0] * 60 ) + $fastest_lap_time[1];
			} else {

				// Deal with times less than 1 minute
				if ( 0 == $fastest_lap_time[0] ) {
					$fastest_lap_time = 0;
				} else {
					$fastest_lap_time = $fastest_lap_time[0];
				}

			}

			// Grab fastest time between qual and fastest lap time
			$time = $fastest_lap_time;
			if ( $qual_time > $fastest_lap_time && 0 != $qual_time ) {
				$time = $qual_time;
			}

			$time = (float) $time;
//echo $time . ': ' . $fastest_lap_time . "\n";

			// If incidents too high, then kick them out
			$incident_ratio = 0;
			if ( isset( $cells[19] ) ) {
				$incidents = $cells[19];

				if ( isset( $cells[18] ) ) {
					$laps = $cells[18];

					// Bail out if they didn't even manage a lap
					if ( $laps == 0 ) {
						continue;
					}

					$incident_ratio = $incidents / $laps;

				}

			}

			// Kick out anyone slow unless they have few incidents
			if ( $time > $time_3 && $incident_ratio > $incident_ratio_1 ) {
				continue;
			}

			if ( $time > $time_1 && $incident_ratio > $incident_ratio_2 ) {
				continue;
			}

			// If no iRating, then set it to 0
			if ( ! isset( $stats[$driver_name]['oval_irating'] ) ) {
				$stats[$driver_name]['oval_irating'] = 0;
			}
			if ( ! isset( $stats[$driver_name]['road_irating'] ) ) {
				$stats[$driver_name]['road_irating'] = 0;
			}

			// Bail out if they don't meet either oval or road iRating minimums
			if (
				$stats[$driver_name]['oval_irating'] < MIN_OVAL_IRATING
				&&
				$stats[$driver_name]['road_irating'] < MIN_ROAD_IRATING
			) {
				continue;
			}

			// Warn about unfound drivers
			if ( ( ! isset( $stats[$driver_name]['road_license'] ) && ! isset( $stats[$driver_name]['oval_license'] ) ) ) {

				// No license so do strict time check
				if ( $time > $time_2 || 0 == $time ) {
					// too slow or no time set, so bail out
					continue;
				} else {

					if ( $incident_ratio < $incident_ratio_2 ) {

						// Not licensed, but fast enough and little incidents, so lets allow them anyway
						if ( defined( 'ALLOW_ROOKIES' ) ) {
							$drivers[$driver_name] = $event;
						}
					}

					continue;

				}

			}

			/*
			SEEMS TO BE ONLY ALLOWING THOSE WITH HIGH OVAL LICENSES
			// Only allow highly rated oval licenses
			if ( isset( $stats[$driver_name]['oval_license'] ) ) {

				if (
					'A' === $stats[$driver_name]['oval_license']
					||
					'B' === $stats[$driver_name]['oval_license']
				) {
					$drivers[$driver_name] = $event;
					continue;
				}

				// Only allow safe D drivers
				if (
					'D' === $stats[$driver_name]['oval_license']
					&&
					$incident_ratio < $incident_ratio_3
					&&
					defined( 'ALLOW_D_LICENSES' )
				) {
					$drivers[$driver_name] = $event;
					continue;
				}

			}
			*/
			if ( isset( $stats[$driver_name]['road_license'] ) ) {

				// Allow A B C drivers
				if (
					'A' === $stats[$driver_name]['road_license']
					||
					(
						'B' === $stats[$driver_name]['road_license']
						&& defined( 'ALLOW_B_LICENSES' )
					)
					||
					(
						'C' === $stats[$driver_name]['road_license']
						&&
						defined( 'ALLOW_C_LICENSES' )
					)
				) {
					$drivers[$driver_name] = $event;
					continue;
				}

				// Only allow safe D drivers
				if (
					'D' === $stats[$driver_name]['road_license']
					&&
					$incident_ratio < $incident_ratio_3
					&&
					defined( 'ALLOW_D_LICENSES' )
				) {
					$drivers[$driver_name] = $event;
					continue;
				}

			}


		}

	}

}


// Specify which to keep
$count = 0;
$listed_drivers = $drivers;
foreach ( $drivers as $driver_name => $track ) {
/*
	ONLY INCLUDES FR DRIVERS, WHIC

	if (
		'formula-renault-last-2' !== $track
	) {
//		echo $driver_name . ': ' . $track . "\n";
		unset( $listed_drivers[$driver_name] );
	}
*/
}

/**
 * Remove existing members.
 */
$users = get_users();
foreach ( $users as $key => $data ) {
	$driver_name = $data->display_name;

	if ( isset( $listed_drivers[$driver_name] ) ) {
		unset( $listed_drivers[$driver_name] );
	}

}

/**
 * Finally, output names.
 */
$listed_drivers2 = 0;
if ( 'csv' === $_GET['pull_names'] ) {

	foreach ( $listed_drivers as $driver_name => $track ) {
		if ( 'personal' !== $track ) {
			echo $driver_name . ',';
			$listed_drivers2++;
		}
	}

} else {
	print_r( $listed_drivers );
}

echo "\n\ncount: " . $listed_drivers2;

die;
