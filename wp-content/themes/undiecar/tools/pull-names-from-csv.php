<?php

if ( ! isset( $_GET['pull_names'] ) ) {
	return;
}


//define( 'ALLOW_ROOKIES', true );
define( 'ALLOW_B_LICENSES', true );
define( 'ALLOW_C_LICENSES', true );
//define( 'ALLOW_D_LICENSES', true );

//define( 'MIN_OVAL_IRATING', 3000 );
//define( 'MIN_ROAD_IRATING', 2000 );
define( 'MIN_OVAL_IRATING', 2000 );
define( 'MIN_ROAD_IRATING', 1000 );



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

$to_be_contacted_in_future = 'Matt Orr,';

// Pre Undie Lights ... 
$personal_contacted = 'Kevan Bingel2,Robert Plumley,Ken Ehlert,Louis Richardson,Andre Moreira,Ben Wheeler,Beto Soussa,Bill Sulouff,Brandon Johnson8,Bruno Romain,Claudius Wied,Craig Crawford,Craig P Kasper,Daniel Wright4,Dave Lodl,Dennis Sather,Dominic Hoogendijk,Floyd Pate,Glen Barrett,Guilherme Carioni,James Craig4,Jamie Brinkley,Jeff Meier,Jelle Verstraeten2,Jose E. Piña,Jose Serrano,Juan Payano2,Kevin McCarthy,Kyle Schuchter,André Heidstra,Luigi Griffini,Marcello Caruso,Marcos Antonio2,Mark A Reed,Mark Voigt,Matthew Randle,Michael Johnson21,Montxo Gandia,Morten Hansen,Neil A. Jackson,Nikolay Ladushkin,Patrick Langley,Paul Rosanski,Ramon Regalado,Randy Parker,Ron Lanzafame,Steven Busuttil,Stuart Lumpkin,Szabolcs Feher,Tom Ecklein,William Norton,Zachary Luctkar,';
$found_in_own_races = 'Harry Damen,Johan Fourie,Finian Dcunha';
$indycar_road_drivers = "Marco Aurelio Brasil,Adam Plunkett,Karsten Brodowy,John Downing,Tim Holgate,Georg Naujoks,Kent Turnbull,Alexander Khursanov,Gary Powell3,A J Burton,chad Trumbla,Per-Anders Mårtensson,Bradley R Smith,Andrey Efimenko,Michele Costantini,David Adams8,Niall McBride,Andrew Kinsella,Matthew Talbert,David Hinz II,Justin Kay,Carl Johnson3,Austin Espitee,Antenor Junior2,Frederick Campbell,Andrew Stone,Serge Cantin,Karl Dronke,Sergio Morresi,Dan Lee Ensch,Rudy Avalon2,Dylan McKenna,Silviu Lazar,Bradley Walters,Andreas Eik,Jean-Marc Brunée,Anthony Cothran,Yves Bolduc,Todd Novasad,Ricardo Rossi,Wade Lear,David Warhurst,Steven Landis,Tom Rowin,Ben Ivaldi,Dimitri Djukanovic,Steves Arvisais,Christopher Hoyle,Travis Bennett,Kevin Sherker2,Tim Williams,Rados?aw Sitarz,Eric Ward2,Chris Stofer,Evan Fitzgerald,Kevin Cornelius,Yuichiro Takahara,John Lott,Zack Ditto,Josh Frye2,Francesco Sollima,Jeramie Horn,Dakota Steven Bowman,John Ahles,Joel Feytout,Andrew Jones6,Nikolay Bogatyrev,Carl Jacolette,Matt Denlinger,Tyrone Harris,Andrew Faryniarz,Tom Kotowski,Jordi Ardid Mendez,Christopher Brown4,Ryan Wilson6,Allan Moreira,Philipp Wigert,M B Dickey2,Henry Bennett,Fernando Guerrero,Rikki Gerhardt,Guillermo Domínguez,Andrea Antongini,Jussi Nieminen,John Burgess,Travis Parker,Dan Barone,Rob Powers,Santiago Nahuel Monente,Guillermo Alvarez,Enric Cabral,Harald Müller,David Henger,Kevin Holzner,JW Miller,Adam Roberson,Thomas Anton Leitgeb,John Merchant,Joachim Brückner,Luis Piñero,Henry White,Pedro Zoffoli,Lincoln Miguel,Richard Browell,Andrew Massey,Christian Steele,Rob Unglenieks,Richard Tam,Troy Eddy,Stephen Warcup,David P Pérez,Andrew Fullhart,Naruko Ishida,Bryan Carey,Doc Stout,Darren Adams2,Thomas Marmann,Richard Kaz,Neil Black,Steven Walter,Francisco Rendo,Casey Drake,Milton Thigpen,Jay Davis4,Wilson Rachid,Drew Motz,Chris Ferry2,Cesare Di Emidio2,Bernhard Jansen2,Trey Mccrickard,Jay Norris,Samuel Etter,Scott McClendon,Kleber Bottaro Moura,Jason Mayberry,Darryn Hatfield,Marcus Wohlmuth,Bob Jennings,Anthony Emery,Samuel Zinski,Pierre Bourdon,John Hess,Philip Eckert,Kaue Gomes,Mitchell Kerstetter,Mitch Walter,William Ruland,Cristian Perocarpi,Daniel Wester,David Riley2,Benton Jones,Albert Oldendorp,Adriano Fraporti,Wolf-Dietrich Hotho,Andrew Nypower,Tom Orr,Steven Clouse,Brian Beard,Rayyan Rawat,John Mignacca,José Godoy,Roger Proctor,Nate LaFluer,Harold L Stevens III,Ronnie Osmer,Brandon Trost,Arnold Estep,Tanner McCullough,Ian Layne,Joshua Witherspoon,Adam Baker5,Juan Riveros Puentes,Patrick Pierce,Blake O''Connell,Dave Jinks,Christopher Demeritt,Joe Branch2,Zach Reinke,Tim Doyle,Alesander Rodrigo,Andreas Werner,James E Davis,Brandon Clarke,Joey Bolufe,Adam Cavalla,Ken Owsley,Bruno Pagiola de Oliveira,Carlos Washington,Jan Penicka,Radek Sykora,Brandon A Taylor,Jeffrey Lacey,Joseph Wheatley,Petri Välilä,Dean Mullins,Joe Burchett,Julian Lavarias,Teemu J Rönkkö,Jason Brassfield,Cristian Otarola,Jared Wishon,Ryan Nolan,Timothy Allen2,Patrick Hingston,Matthew Montis,Carlos Neto,Christian Cabangca,Troy Thiem,Jens Roecher,Patrick Byrne,Kai Tröster,Justin Fortener,Alexander Knisely,Tyler Langenberg,Stephane Parent,Joonas Kortman,Jefferson Padovani,Kim Short,Said Gonzalez,Caeton Bomersbach,Jorge Marquinez,Olivier Dean2,Rodrigo Azevedo,Humberto Rattmann,Marco A Pereira,Wayne Sanderson,Alex Everitt,David Altman,Jeff Pritchard,Ryan Junge,Geoffrey Cervellini,Ludovic Mostacchi3,Ralf Schmitt,Nicolas Guarino,Mirco Comitardi,Anthony Gardner3,Julian Wörner2,James Robinson11,Andrew Aitken,Ronald Goodison,Tracy Drummond,Bob Martin,Jason Cange,Jeremy Hartman,Mertol Shahin,Stuart McPhaden,Peter Grey,Tim Kay,Oliver Elsen,Soeren Kolodziej,Mads B L Hansen,Jacob Yuenger,Darren Leslie,Stefan Remedy,Julian Wagg,Federico Calderon,Felipe Kaplan,Ernes Romero,Andrew S Brown,Douglas Rice,Richard da Silva,Jagoba Merino4,Jerry Foehrkolb,Todd Schneller,Joseph Plante,John Garrett,Trevor Avery,Migeon Johnny,Radoslaw Ciszkiewicz,Bill Fisher,Jake Henry,Clifford Ebben,Kian Raleigh-Howell,William Stroh,Christopher Mattas,Luis Pedreros,Jordan Lubach,J Santos,Ryan Lewis,Kyle Selig,Carlos Buritica,James Kilburn,Colin Appleton,Adam Hackman,Nico Rondet,Rodrigo Munhoz,David Corpas Benitez,Derin Pitre,Neil Andrews2,John Keefe,Andrew Cauffiel,Ryan Stein,James Woods3,Trevor Fitz,Arthur Rast,Karl Schwing,Mike A Taylor,Chris Reid,Mikey Olson,Brendan Juno,Robert Nuernberg,Timothy Roberts,Javier Garrido Vaquero,Leonardo Marques,Victor M Cano,Alexandre Martins G,Luis Sereix,Tyler Worrall,Lucas Stattel,Erick Davis,Garrett Konrath,Lucas Louly,Scott Faris,Bobby Maiden III,Brian McCraven,Andy Chadbourne,Tommy Farris,Alexandre Vinet,Jeff Yeager,Mar Vozer Felisberto,David Ross,Heamin Choi,Gage Rivait,Guillaume Pelletier,Anthony Mino,Douwe Tapper,Sam Adams,Davis Trask,Gonzalo Romero,Jacob Phillips,Michael P McVea,Justin Weaver,Brenden John Koehler,John-Paul Bonadonna,Tyler Stacy,Collin R Stark,Nicholas Soriano2,Donovan Piper,Joey Lamm,Matt M Adams,John M Roberts,Jacob Gordon,Simone Nicastro,Martijn Nagelkerken,Corey J Ott,Ricky Heinan,Marco Colasacco,Andy Crane,Sergio Lamares,Jim Flippin,Scott Beck,Paul Parashak,Brian Rainville,Yanisse Ameurlaine,Senad Kocan,Charles Crump,Blair Hamrick5,Todd Broeker,Pat Copley,Jordan Pruden,Nicholas Schmieg,Jacob Young,Andres Espinoza,Justin Parcher,Lee Hamlet,Jacob Schneider,Massimo Duse,Austin H Blair,James Pandolfe III,Ross Olson,Andy Rhodes,Christopher Hussey2,Tobias Brown,";
$laguna_seca_dallara_dash_drivers = 'Joshua S Lee,Craig Shepherd,Derek Hartford,Kevin Vogel,Eneric Andre,Alex Bosl,John Dubets,Jimmy Duncan,Albert Gisbert Falgueras,Sam Rosamond,Elliott Skeer,Robert Ulff,Oliver Patock,Robert Draper,Bruce Granheim,Mike Medley,Yusef Rayyan,John Szpyt,Nash Fry2,Barry Arends,Collin van Raam,Carl Modoff,Chris Meeth,Anver Larson,Alex Millward,Craig Evanson,Gulas Mate,Claes Poulsen,Tomasz Kordowicz,Maurice Gomillion,Brett Gardner,James Osborne4,Sepp Odoerfer,Kevin Hollinger,Craig Forsythe2,John Ellison,David Bessa Dias,Benjamin Cox,Rebelo Romain,Jeroen van Wissen,Arjan de Vreed,Gary Krichbaum2,Jonathan Heimbach,Nicholas Millard,Jake Hewlett,Alain Stoffels,Attila Papp Jr,Eddy Andersson2,Gabriel Garcia Olivares,James Strobel II,Kevin Giménez,Thibault CAZAUBON7,Derek Adams,Joris Valentin2,Robert Siegmund,Hildebrando Pinho Junior,Michael D Myers,Ben Clayden,Rob Donoghue,Elias Viejo,John Signore,Davis Rochester,Alexandre Gramaxo Gouveia,Jacob Bieser,Susan Blackledge,Robert B Eriksson,Alexandre Gravouille,Cody Siegel,Jan Spamers,Richard Sudduth,Szilard Halaszi,Shaun Barrowcliffe,Anton Kusmenko,Pierre Verne,Ryan Schartau,Florian Bayle,Vincent Hamet,Guillaume Dupont2,Imre Lukacs,Romain Pelissier,Xisco Fernández,Loris Amadio,Benjamín Carreiras,Andre Monteiro2,Robert Long2,Braden Graham,Duncan Watt,Paul J Ulliott,Tom Berendsen,David Strickland,Antonio Bermúdez,Scott Nicholson Jr,Adolf Egli,Sergi Maturana,Manuel Bañobre,Quinten Vermeulen,Javier Perez M.,Frederick Zufelt,Brendan Lichtenberg,Arjan de Vreede,Sebahattin Atalar,Raphael Lauber,Christian Rose2,Paul Huber,Simon Etheridge,Lawrence Phipps2,Javier Isiegas,Tye Macleod,Lee Jenner,Fran Lucas,Lukas Winter,Ryan Bird,Joel Stampfli,Zachary Sober,Robert McNeal,Brad Teske,';
$promazda_drivers = 'Joshua S Lee,Craig Shepherd,Derek Hartford,Kevin Vogel,Eneric Andre,Alex Bosl,John Dubets,Jimmy Duncan,Albert Gisbert Falgueras,Sam Rosamond,Elliott Skeer,Robert Ulff,Oliver Patock,Robert Draper,Bruce Granheim,Mike Medley,Yusef Rayyan,John Szpyt,Nash Fry2,Barry Arends,Collin van Raam,Carl Modoff,Chris Meeth,Anver Larson,Alex Millward,Craig Evanson,Gulas Mate,Claes Poulsen,Tomasz Kordowicz,Maurice Gomillion,Brett Gardner,James Osborne4,Sepp Odoerfer,Kevin Hollinger,Craig Forsythe2,John Ellison,David Bessa Dias,Benjamin Cox,Rebelo Romain,Jeroen van Wissen,Arjan de Vreed,Gary Krichbaum2,Jonathan Heimbach,Nicholas Millard,Jake Hewlett,Alain Stoffels,Attila Papp Jr,Eddy Andersson2,Gabriel Garcia Olivares,James Strobel II,Kevin Giménez,Thibault CAZAUBON7,Derek Adams,Joris Valentin2,Robert Siegmund,Hildebrando Pinho Junior,Michael D Myers,Ben Clayden,Rob Donoghue,Elias Viejo,John Signore,Davis Rochester,Alexandre Gramaxo Gouveia,Jacob Bieser,Susan Blackledge,Robert B Eriksson,Alexandre Gravouille,Cody Siegel,Jan Spamers,Richard Sudduth,Szilard Halaszi,Shaun Barrowcliffe,Anton Kusmenko,Pierre Verne,Ryan Schartau,Florian Bayle,Vincent Hamet,Guillaume Dupont2,Imre Lukacs,Romain Pelissier,Xisco Fernández,Loris Amadio,Benjamín Carreiras,Andre Monteiro2,Robert Long2,Braden Graham,Duncan Watt,Paul J Ulliott,Tom Berendsen,David Strickland,Antonio Bermúdez,Scott Nicholson Jr,Adolf Egli,Sergi Maturana,Manuel Bañobre,Quinten Vermeulen,Javier Perez M.,Frederick Zufelt,Brendan Lichtenberg,Arjan de Vreede,Sebahattin Atalar,Raphael Lauber,Christian Rose2,Paul Huber,Simon Etheridge,Lawrence Phipps2,Javier Isiegas,Tye Macleod,Lee Jenner,Fran Lucas,Lukas Winter,Ryan Bird,Joel Stampfli,Zachary Sober,Robert McNeal,Brad Teske,';
$me = 'Ryan Hellyer,';
$phoenix_dallara_dash_drivers = 'William Swenson,Donnie Sanders,Liam Quinn,Steven Freiburghaus,Kurtis Mathewson,Joao Valverde,Bryant Ward,Tarmo Leola,Michael Kildevaeld,Garrett Cook,Hartmut Wagner,Alejandro Leiro,Frank Bieser,David Keys,Balazs Floszmann2,Maxime Potar,Aymerick Vienne,Jason Lowe4,Kevin Shannon,Henry Eric,Patrice Lebrun,Rémi Picot,Timothy Scanlan,Philippe Tortue,Antoine Gobron,Neil Thompson,Pascal Bidegare,Mathieu NEU,Kirk Smith,Laszlo Miskolczi,Sam Cook4,Jim Gibbs,Dale Robertson,Kelly Thomas,Adolfo Macher,Denis Nestor Kieling Kieling,Maik Lara Guerra,Richard Grimley,Herve Lanoy,Jesus Fraile Hernandez,Justin Adakonis,Matthew Carter7,Art Seeger,Martin Vaughan,Rob Collister,Jake Johannsen,Tyler Rahman,Jan Schumacher2,Esa Hietanen,Vahe Der Gharapetian,Jake Conway,Fernand Frankignoul,Daniel Förster,Joel Taylor,Marti Olle,Patrick Weick,Thierry Schmitt2,Rodney Bushey,Michel Rugenbrink,Zack Tusing,Trever Halverson,Peter Labar2,Steven Roberts4,Dirk Rommeswinkel,Daniel Redlich,Brian Spotts,Isaac Jaen,GÃ©rard AMBIBARD,Pablo Perez Companc,Robert Queen,Dewey Perry,Joshua Halvey,Charles Hinkle,Helio Santos,Austin Collings,Adam Smith5,Austin Eder,Joshua Baird,Harry Floyd,Joseph Scatchell,Dardo Nosti,Mario Alvarez,Michael Erian,Colin Earl,Kenneth Webb,Dave Dawson,Paulius Dunauskas,Trevin Dula,';
$formula_renault_last_2 = 'José Cantharino,Michael H Burton,Tae Kim,Mauricio Moreno2,Rob McGinnis,Chester Thompson,Pavel Philippov,Tony Vasseur2,Phil Letchford,Michelle Smeers,Jesus Manuel,Greg Garis,Jack Freke,Daniel Nyman,Dan Mocanu,Paul Nopper,Walter Bornack Jr,Charlie Rage,Augusto Henriques,Marco Van Wisse2,Saúl Quintino Santana,Vittorio Saltalamacchia,Rene Maurer,David Rojo Lopez,Cameron Dance,Kris Roberts,David VELASQUEZ,Viktor Shubenkov,Alexander Stock,Jerome Haag,Matt Bobertz,Fabio Aoki,Admir Nevesinjac,Stefano Calascibetta,Christopher Gray3,Marc Oros,Don Bowden,Andy R Moore,Roger Prikken,Naiche Barcelos,Daniel Berry,Fredrik Kvarme,Sergi Viñolas Font,Paul R Lewis,Marcos Silveira2,Curtis Martin2,Adam Leff,David Hoffmann,adam Saunders2,Ranford S Brown,Matt Cox,Salva quadradas,Terry Taylor,Sergi Cardó Catalan,Chris Homann,Reid Harker2,Andy Bonar,Tony Matthews,Artur Gozdzik,Manuel Heuer,Joonas O Vastamäki,David Waldoch,Lubomir Moric,Egoitz Elorz,Daniel Barrero,Scott Clarke,Gerald Zindler,Sandro Biccari,Rogier Visser2,Kouji Itoh,Takuya Sekimoto,Antonio Ortiz Poveda,Alexis Mattaruco,Francesco Tonin,Philippe Silva,Thomas Glad,Scott McClintock,Thiago Spencer,Damon Martinez,Jeremy Skinner,Tim Delisle,Jeroen De Quartel,Chris Burgess,Jerome Lapassouze,Jonas Toigo de Souza,Andrea Brachetti,Rogerio Schiavon,Marcio Marodin,Kristof De Busser,Adam Frazier,Antony Perryman,Angus Waddell,Tim McIver,Nikolay Andreev,Jorge Rolandi,Jorge D Romero,Dave Martin,Kevin Fennell,Sebastien Kendzierski,Stuart John,Thomas Scheuring,Lopez Alexandre,Angel Garrido Orozco,Bruno Domiter,Dimitri Coulon,Alejandro Blanco,Carles Bayot,Hannes Wernig,Bruce Keys,Marc Leyes,Ronnie Olsen,Caroline Viscaal,Janneau Rompelberg,Dennis Gerressen,David Buitelaar,Jakub Jezierski,Sakiran Partowidjojo,Domenic Guras,Daniel Gazquez,Christian C Meyer,Raul Alfonso Valero Llordes,Fabrizio Donoso,Peter Berryman,Frank van Brandwijk,Phil Reid,Claudio Candio,Martin Donati,Charles Stark,Anibal Colapinto,Teemu Kotila,Adam Tierney,David Porcelli,Patrick Wartenberg,Carlos Cabral,Marcos Marcelo,Claudio Costarelli,Alex Düttmann,Bruce Poole,SeongWon Jeong,Ossi Varjoranta,Steven Campbell,Mario Ledesma,Ken Daly2,Joseph Nelson2,Paul Trgovac,Daniel Cunha Prado,Miroslav Ju?í?ek,Aaron Turner,Pedro Henrique Moisés,Frederic Guillaume,Edward A Samborski,Derwyn Costinak,Jose Alberto Hidalgo Nicolas,Brent Mills,Cuba Gonzalez,Alberto Pérez,Evan RT Imray,Giancarlo Bartolini,Jack Hobbs,fabrice BAZIN,Johan Hellemans,Robert Dilbeck,Davide Giovanni Solbiati,Thomas Mrazik,Alejandro Alvarez3,Sam Satchwell,Patrick Luzius,Oscar Quiroga,Richard Schouten,Ralph Thomson,Alex Stumpe,Anto Mn,Erkka Lindström,Renaud Soudiere,David Jarvis,Victor Martins,Christian Schultz,David Sanchez5,Daniel Clarke,Jose Pleguezuelos Langa,Stéphane LEMAIRE,Edmundas Azlauskas,Hiroshi Sakai3,Marco Cesana,Jari Viinamäki,Mickel Francis,Josh Slade,David Thompson12,Kurt Bagnell,Dylan Prostler,Sergio Pasian,Jose Telmo,Elliot Leach,Scott Lefeber,Alan Hix,Marc Martinez Forner,Jeremy Lapainis,';
$hosted_sessions = 'Jennifer Kind,Lauralee Blackburn,Charlene Swinehart2,Ronda Boxler,';
$spa_indycar_drivers = 'Rados?aw Sitarz,Henry Bennett,Arturo Amro,Carlos López,Hercules L. Santos,Carlos Washington,Pebst Augusta,Antenor Junior2,Lucas Stattel,Chris Langswager,Mauricio Moreno2,Rob Unglenieks,Nelio Cunha,Vinicius Marega,John Ahles,Bart van Velzen,Jared Dziedzic,Brian Beers,Radek Sykora,Tae Kim,David Adams8,Ralf Schmitt,Lance Simon,Patrick Moose,Martin Kober,Stanley Sullivan,Andreas Eik,Domingos Frias,Albert López,Carl Barrick,Gerald Zindler,Enrique Tramontin,Jeffrey howard2,Rick Hundorfean,Gary Borkenhagen,Thiago Bello,Tyson Sailer,Krysta Nelson,John Erickson,Donnie Shealy2,Andy Rhodes,Alejandro del Campo,Kurtis Mathewson,Simon Briant2,Jackson Patricio Dutra,Andre Moreira,Bill Gallacher Jr,Ronald P. Brent,Damon Martinez,Christian Steele,Austin Espitee,Andrey Efimenko,James E Davis,Andrew Burns2,Rikki Gerhardt,Richard Kaz,Henry White,Don Jareño Villarreal,John Downing,Andrew Kinsella,Joe Flanagan,David Wall Jr,Kim Short,Nolan Baltz,';
$randoms = 'Bryant Ward,Kevin T Firlein,Lisa Ryan,Henry Bennett,Austin Espitee,Richard Tam,Andrey Efimenko,Martin Kober,Sergio Morresi,Pebst Augusta,Bill Gallacher Jr,Carlos López,Jeffrey Oakley,Carl Barrick,Vinicius Marega,Kenneth Bafford,Travis MacChesney,Thomas Connolly,Isaiah Locklear,Michael Elsom,Patrick Collins,Garrett Cook,Gail Walker,Philip Moore3,John Macchione,Richard Dempsey4,John Chapman4,Austin Johnson6,Stephanie Lessentine,Laura Lawson,Jani Penttinen,Peg Mulrooney,Quinn Johnston,Courtney Terrell,Lauri Salo,Jessyka Vox,Andrea Guglielmetti,Jani Järvinen,Alysson Pereira,Jone Kaijanen,Tommi Talonen,Angel Ledesma,Christine Marie Tillmann,Andrea Baldi,Avery McDonald,Marie Hruschka,Ellis Teal,Oakley Higham,Karlyn Lyneis,Angel Fernandez Cobo,Elaine Krizenesky2,Leila Wilson,Lauri Happonen,Emily Jones,Terrie Blackburn,Monica Clara Brand,Angelika Pavlowski,Yanick Compostel,Rebecca Pauline,Courtenay Smith,Bas Slob,Briar LaPradd,Hena Hakkanen,Laureen Woods,Kalle Ruokola,Kelly B. Crabtree,Erin Nagy,Jordyn Charge,Kendall Frey,Jade Williams,Andrea Girella,Jady Baumgardner,Sasha Todorovic,Alysson Pacheco2,Niki Suhonen,Elizebeth Muscat,Kendall A Nicholson,Lauri Linna,Tommi Vacklin,Kenzy Nieuwhof,Anneli Jakobsoo,Andrea Gatti,Mackenzie  Korince,Sarah Laprevotte,Saby Hanyik,Jani Koskinen,Daniella Blanco,Kari Nyman,Ariel Acastello,Kelly Newman,Kelly Wilson,Sasha Milosavljevic,Rylan Furler,Madison Casey,Jann Dircks,Skyler Grissom,Lauri Ketola,Vail Riches,Jani Polameri,Daniela Azunaga,Deborah Soete,Kelly Niquette,Valerie Cote,Elena Gomez,Yelena Medwedewa,Laura Bond,Kendall Shaw,Nicole Johnson,Bas Neijenhof,Kaitlyn McDade,Jennifer King,Matti Höylä,Tamy Accioly,Jessie Rougeux,Andrea Stefanina,Ariel Eduardo Bernardi,Andrea Giachè,Payton Weakley,Angel Dean,Sky Willis,Jannis Koopmann,Jocelyn  St-Martin,Danni Fugl,Kalle Valli,Christel van Essen,Kelly Kozek2,Angel Moreno Garcia,Julie Redmon,Andrea Disarò,Sandra Castrogiovanni,Jani Kattilakoski,Tomi Mäkinen,Sarah Souders,Kari Ikonen,Lauri Hiljanen,Andrea Boccellari,Lisa Ryan,Laura Perry,Kenley Brown,Lauren Hoock,Brynn Pearce,Jade Buford,Andrea L Bozzer,Ariel Alaniz,Magalie Damermant,Floris de Wit,Jani Rautio,Raffaella Cucciarre,Andrea Scognamillo,Jani Pitkonen,Kelly Sprayberry,Ariel Doman,Jani Smolander,Sasha Anis,Andrea Lojelo,Yury Korn,Kelly Dahl,Vivian Santiago,Amoreena Hall,Molly Steinberg,Tomi Rostén,Angel Hernandez,Tommi Saunter-Chun,Janis Vigulis,Ingrid Marti,Niki Loipold,Kalle Peltonen,Andrea Corsetti,Matti Huotari,Matti Klami,Matti Koskinen,Kari Kalen,Klaudia Monica,Tommi Nieminen,Amber Laybourne,Elis Jackson,Lindsay Barrie,Julia Pros Albert,Ariel Hartung,Hannah Lewis,Jone Randa,Kelsey Roach,Andrea Fraccalvieri,Angel Pina,Reese Starowesky,Tomi Virtanen,Gillian Woods,Kimber Jansen,Skylar Dunning,Stefanie Fleper,Angel Felipe Nuño Garcia,Bas Westerik,Tomi Salmi,Lauri Haverinen,Cherie Mollohan,Conny Naslund,Remy Lozza,Remy Provencher,Floris Bieshaar,Kalle Vaimala,Marjan Koderman,Tomi Hannuksela,Jani Kankaanpää2,Andrea Frollo,Sage Karam,Sara Dove,Angel Broceno,Denise Hallion2,Ellen VanNest2,Anne Struijk,Angel Lozano,Sian Walters,Kari Kiviluoto,Alyssa Ferrie,Tomi Mairue,Gwen Kolsteren,Dalila Magdu,Andrea Stefanoni,Carmen Comeau,Andrea Del Piccolo,Nelli Pietro,Jennifer Grifhorst,Lari Niskala,Pier Andrea Cappelletti,Tommi Seppänen,Sabrina Gramß,Laura Zampa,Angel Rosa2,Susi Badia,Chandra Wahyudi Jong,Jocelyn Lauzière,Sasha Ebel,Rus Marius,Lauri Mattila,Andrea Masiero,Andreia Azevedo,Sena Pugh,Kari Koski,Montserrat Cervera,Kelly Knight,Ally Sinclair,Lisette Davey,Matti Mäki,Bas Metselaar,Renie Silveira Marquet Filho,Kaya Selcuk,Niki Djakovic,Matti Räty,Tomi Ojala,Jani Petäjäniemi,Sindy Lajoie,Alison Willian Gomes Dos Santos,Niki Kresse,Agnes B Kaiser,Shelby Blackstock,Leila Hammadouche,Kari V. Saastamoinen,Dian Kostadinov,Tommi Piekäinen,Lindsay Wauchop,Reiko Arnold,Myung Kun Shin,Sofia Stella,Kari Ontero,Pascale Paquet,Joyce Martin,Holly Crilly,Sunday Awoniyi,Andrea Melonari,Jani Nurminen,Maris Sulcs,Jessica Dube2,Andrea Lo Presti Costantino,Delaney Mulholland,Amal Youness,Tomi Leino,Andrea Donati,Bas Rowinkel,Kalifa Dong,Jonni Molyneux,Jani Vähäsöyrinki,Hedwig Gager,Tommi Ojala,Lark Mint,Ilham Halabi,Ronni Brian,Kalle Lehtonen,Kalle Uusitalo,Nathalie Gaubert,Andrea Rizzoli,Angel Saez Juan,Andrea Zecchetti,Georgi Dimov,Andrea Schilirò,Angel Banegas,Niki Jessen,Jani Mikkola,Angel Hernandez Corte,Kimm Johansson,Vivian Santiago,Amoreena Hall,Molly Steinberg,Ingrid Marti,Amber Laybourne,Julia Pros Albert,Hannah Lewis,Gillian Woods,Kimber Jansen,Skylar Dunning,Stefanie Fleper,Lauri Haverinen,Cherie Mollohan,Conny Naslund,Sara Dove,Denise Hallion2,Ellen VanNest2,Anne Struijk,Sian Walters,Alyssa Ferrie,Gwen Kolsteren,Nelli Pietro,Jennifer Grifhorst,Sabrina Gramß,Laura Zampa,Susi Badia,Jocelyn Lauzière,Sasha Ebel,Lauri Mattila,Ally Sinclair,Lisette Davey,Sindy Lajoie,Alison Willian Gomes Dos Santos,Agnes B Kaiser,Leila Hammadouche,Sofia Stella,Joyce Martin,Holly Crilly,Jessica Dube2,Nathalie Gaubert,Kimm Johansson,';
$skip_barber = 'Jaime Simonet,Andrew Procario,Edwin Vallarino,Francois Boulianne,Miguel Garcia sanchez,Isaac Silva Gonzalez,Gunnar Moller,Carlos Ventura,Richard Martinez de Morentin Suescun2,Thomas Merrill,Alex Baciu,David Miller10,Brad Henry,Muhammed Sahan,Alan Gomez Selfa,Chris Malcolm,Matt Malone,Stephan Bloechlinger,Adrian Garcia Cruz,Xavier Bertrant,Michael Morrison2,Kane Halliburton,Sébastien PETIT,Mika Johannes Kovanen,Andrew Horsley,Angelo Abarca,Charles Anti,Gordon Ramsay,Richard Warmingham,Romain Marchal,J. Félix Díaz,Jesus Menendez,Adam Rylance,Andre Castro,Manuel Valente,Nahum Solà,Llyr Hughes,Bruno Barbugli,Jack Turner,Wolfram Fiedler,Lucas Loyarte,Tore LÃ¸kken,Richard Bagshaw,Giovani Diaz,Fernando C Rodriguez,Michael Baley,Daniel Cabanillas,Don Yuhas,Steven Patmore,Daniel Behrensen,Roman Cajka,Karthik Pai,Neil Bontems,Derek Issa,Peter Cnudde,Jonathon Sheen,Tyler Tucker,Álex Ardisana,Mario Gil,Bryan Krauss,José Manuel Esteban,Michel Cozzone,Sergio R Garcia,Ruben Dominguez Prieto,Agustin Martos,Juan Carlos Marquijano,James Flannery,Gavin Newcombe,Thiago Canola,Iban Etxeberria,Tibor Sandor,Alexander Lauritzen,Julian Bell,Mark Brightman,Rubén Fernández Muñiz,Dylan Francis,Dave Chv Taylor,William Hartman2,Matthew Woollett,Elvis Allende,Oliver Fuentes,Bob Kern,Tyler Hervias,Alberto Pellegrini,Garth Galinat,Dave Boyle,Federico Leo2,';
$skip_barber2 = 'Greg Seitz,Johnny Guindi,Jaume Dalmases Torres,Tuan Tran,Justin Tipton,Mike McCormick,Iriome martin,Tim Beaudet,Jarrod Marks,Vicente Gascon Marti,Fahim Antoniades,Julien Lardy,John Ehlers,Binesh Lad,Steve Owens,Johan Lespinasse,Michael Guariglia,Dave R Roberts,Santiago Rodriguez,Federico Montini,Mark Chorley,Sara Savage,Sean Dittmer,Edward Torres,Craig Foster,Paul Spencer3,Andrew Bolton,Diego Palomar,Sean Michael Jr.,Tomas Marcos,Jaime Fierro,Harley Lewis,Tom Ward,Ivan Barreira,Jimmy Anthony,Brenden Campbell,Carlos Serantes,Kevin McKnight,Oscar Tolnay,Fernando Vega,Daniel Ackland,Anthony Peeble,Steven Brumfield,Joao Oliveira,Eric Schulhof,Steven Wareham,Dave Price,Robson Cardoso,David Kerouanton,David Workman,Alberto Veiga Grana,Maxime de Tilly Brisebois,Zoltan Herczeg,J william Smith,L Leroy Coppedge,Michael Heuschele,Adailton Santana,Nick Ritcey,Pete Tinkler,Andrew Love,David C Payne,Mike Devereaux,Curtis Thomason,Jim Brewster,Jon Sully,Israel Perez,Alberto Medina,Patric Fairbanks,Benn Williams,Xavier Baboulaz,Nicholas Vaughan-Roberts,Aitor Lejarraga,Jochen Schächtele,Alexander Honing,Santiago Cabrera,Daniele Forgiarini,David Bragg,Racim Fezoui,Emilio Romero,';
$skip_barber3 = 'Dennis Johansen,John J Kern,Bryan Sutton2,Rick Tarvin,Daniel Rivito,Luiz Gonzaga Filho,Vivek Reddy,Patrik Flis,Ryan G Walker,Ricardo J Faria,Eduardo Prado,Rafael Torres Castaño,Jackson Neesley,Xavier Solà,Stef Veenhof,Ivan Montoya,Simon Edwards,Kimmo Vierimaa,Stephen Bair,Angelo Cellura,Timothy Roman,Bicor Valencia,Jesus Martinez Hort,Cédric Gesmier,Peter Lo,Jean-Francois Boscus,Janne Vaarasuo,Mihail Latyshov,Mario Civera,Jesús Montiel,Gerard Florissen,Gregory Thompson,Ville Poutiainen,Ric Davis,Jukka Korvenranta,Jaime Ballesteros,Hiroshi Nobuoka,Daryl Ridley,Vicent Roig,Bart Martinovic,Graham Marshall,Ian Haycox,Miguel Aguilar,Matthew Woodley,Jhonathan Selas Sanchez,Johnny Voegeler,Michael Parsons2,David Sitler,Richard Franco,Hector Soler Font,Francesc-Xavier Casado,Arnaud Chambefort,Cyril Broeders,Gary Thomas,Steffen Bremer,Kerem Avan,Felipe Nuño,Samuel Soto,Tomek Weber,Andreas Arvanitantonis,Antonio Jesús Villalón,Iñigo Bea,Ismael Habib Jiménez,Michael Rattigan,Sergio Fernandez,M.A. Cabo,Manel Segret,Pascal Van den Hoek,Ivan Mula Vivero,Joeri Cox,Martin Turner,Yuuto Fukuchi2,Nicklas Gjerulff,David Arnold,Herve Boyard,Marc Gammack,Ismael Pereira,Christopher Kasch,Ashley Higham,Jos Smets,Bjoern Reddehase,Christopher Zoechling,Brice Michelon,Barry West,Christian Meese,Endre Papp,Alex McKellar,Cooper Webster,Paul Rooks,Phillip Worley,Eric Violett,Sergio Aldaz,Jonatas Silva da Costa,Kevin Huang,Luke May,Bruno Gallego,Alexis Demers,Shinji Kito,Jason Pitts,Andrew B Whitehead,Johannes Kok2,Philip Robertson,Ozgur Basboyuk,Costa Andrea,Evaristo Gonzalez Ruiz,Graham Forrest,Raul Sanz,Richard Yalland,Antonio Rita,Carlos Rodriguez Martin,Ricardo Gama,Gustavo Vallerio Mundici,Oscar Vall Gallén,Ryan Williams3,Matthieu Fourtemberg,Nick Carty,Nathan Brown6,Johan Poggi,Junior Yearwood,Steffen Seljeskog,Simón Durán Toledo,Vincent Lemarchand,Thomas Zieger,Alejandro Carrasco,Kheireddine Bouafia,Bob Batalla,pascal Metselaar,Fernando Casaus,Joe Cole,Roberto Martin,JB Massida,Bastien Remise,Christian Nilsen,Jordi Moretó,Lewis Parin,Shigeru Ogawa,Cavan Taylor,Gareth Lavelle,Gary Wolboldt,Sam Dobie,Jeroen Ronsmans,Harry Fuchs,James Mckie,Alberto Segovia,Ashley Work,Michael Jones13,Jacob Jacobsen,Joseph Oetzell,Dean Malone,Alan Catale,Glenn Croswhite,Grant Bray,Andrea Ventura,Kent Connolly,Cody Bolinske,Rodrigo Capeleto Ferreira,Lucas Stewart,Austin Zetzman,Cristian Gavin Alvarez,Arnaldo Petcov,Manuel J. Lopez,Ron Borden Jr,FT Fenaux,Jesus Perez Paredes,Maikel Rodriguez Fuentes,Sergio Hidalgo,Alex Saunders,Jose Miguel Rosillo Nieto,Casper de Kort,Karl Hammarling,Neil Middleton,Mark J Turner,Oriol Espona,Marko Hyypia,Jesper Giortz-Behrens,Jens Paul,Ezequiel Beltramo,Kristofer Moreau,Christian Olsson,Alan Needle,Calvin Allison,Andy Baker,Robert Mason2,Marcelo Couto,Alberto Jiménez Sáenz,Jason Coetzee,Arne Stehn,David Peña Jimenez,Nico Roman,Michael Messenger,Viktor Nagy,Sami Sallinen,Robert Boaden,Craig Platts3,Ricardo Margarida,Mikael Engstroem,Juan Aragones,Motonori Handa,Chris Polley,Steven W Thomas,Pep Meyer,Ian Robertson,Miha Filej,Michael Fiedler,Luke Muir,Brion Sohn,Nim Cross Jr,Chris Tweney,Rafael Limon,Nick Read,Nixon Montero Ugalde,Peter Stubbins,Robin Luckey,John Battista,Matthew D Billings,Paul Godfrey,Matthew Murphy2,Joanmi Marzo Gil,Ray Ehlers,Zachary French,Brian Heiland,Austin Hervias,Diogo Francisco,Richard Doughty2,Edu Pacios,Mike Swenson,Aldis Polis,Javier Damlow,Michael Smith41,Emanuele L Mambretti,Mark Cossins,Fabian Menetrey,Damien Devaux,Edward Bewers,Dave Killens,Antonio Diaz Estevez,Antonio De Marchis,Samuel Reiman,David L Hicks,Victor Suarez Rivero,Jose Arbos,Ricardo P Silva,Charles-Eric Lemelin,Darren Lessue,Dexter Cutts,Alex J John,Pavel Suchacek2,David Strathern,Tim Berti,Chris Bland,Cory McLemore,Alberto Mangual,Jerry Schuryk,Jerad Sharp,Shawn Noble,Matthew Murphy4,Jeremiah McClintock,Wesley Winterink,Paul Barnett,Hunter Reeve,Jorge Reinoso,RJ Bishop,Scott Malcolm,Theodore Burns,Ben Glenton,Lorenzo Garcia Mira,Jan Hoffmann,Oscar Artiñano,Niels Clyde,Adrian Brzozowski,Marcel Seidel,Andreas Robertsson,Dave Gymer,Mario Nuñez Nieto,Mark Smith,Michael Sullivan,Adrian Vila,Ronald Rasmussen,Fabio Rühl,Tino Gosselin,Edward Tink,Jochen Büttgenbach,Stuart Bradley,Jason Warren2,Paul Nichols,Bastiaan Huisman,Joe Bradley,Sota Muto,Phillip Stoneman,Dan McGuirk,Kirk Lane,William L Smith,Duane Benzinger,Johannes Wellhöfer,Marcelo Pellegatti,Martin Cruz,Casey Black,Arturo Cruz Ramos,Ben Watkins,Ashley Beard,Kristo Chinmai,Håkon Grebstad,Peter Classon,Hans Heuer,Carlos Quilez,Anthony Catt,Alfredo Malo R,Guille Garcia,Tim Hendrixen,Robert Fox4,Borja Padilla Marrero,Mark Driessen,Gonzalo Camara,Mat Ishac,Barry Langford,Daniel Lezcano Manso,Graeme Hudson,Jason Tyler2,Masaki Tani,Luke Whitten2,Jacques Swanepoel,Mark Jeffery,Mario Visic,David Litzistorf,Mick Grey,Marcos Bodas5,Phil Lee,Gregory Doan,Kevin Henderson,Philippe Leybaert,Jose Portilla,Brian Leavitt,Steve Ficacci,Andreas Andersson3,Alex Clarke,Phillipp Urquhart,Ken Kurtz,Fran Gongora,Koldo González Gomez,Lando Norris,Kelvin van der Linde,Jeff Deliere,Teemu Vaskilampi,Oleg Melihov,Alex Kattoulas,Jon Scholtz,Daniel Edmonds,Alberto Cerda,Keith Sharp,Tim Huss Jr2,Wolfgang Wildenauer,Jonathan Chasteen,Todd Ingves,Raico Álvarez Feijoo,Hector Balaguer,Les Peck,Michael Morris6,Andrew Lawler,Carlos Kac,Craig Spitzer,Daniel Williams4,Robert Young,Ferran Serra Cercos,Martin Brandon,Denis Fricher,Carlos Arnau Ros,Steven Gatesman,Róbert Sebestyén,Mario Díaz,Juan Bleynat,David Harney,Claudio Monteiro2,Jesper Kosse,Jose Luis Lopez Abellan,Nick Sigley,Luis Hernandez Sanchez,Gabriel Farias,M.C. Visser,Charlie Kerschbaum,Andrew Brewer,Nicolaj Appelby Hansen,Harrison Finch,Yuuya Kimura,Stuwie White,Loic Villiger,David Goldaracena,Jan Aleksandrov,David Juez,Stephen Jenkins,Kyle Trudell,Paul Wood-Stotesbury,Michael Pianalto,Jimmy Boylan,Brent Wilson2,Doug Metzel,Marko Perich,John Oliver6,Jeremy Hobson,Matthew Shanks,Steve Kagerer,Alexandre Lorenzini Crespo,Bryan J Kelly,David Hickman,Tiago Pires,Zachary Buchanan,Mike Paschen,Bruno Lambrecht,Alberto Doñoro,Daniel Mesa Ramirez,Kenny Bairolle,Nick Oneill,Franck Levasseur,David Ballew2,Marcel van Bloppoel,John Hall3,Luis Leal,Charles T Jordan,Karl Handley,Armando Luque,Nick S Curry,Elijah Bautista,Christopher Lawson3,Oriol Moret Alcalde,Jared de Kruijff,Camille Younan,Christian Greule,Sergi Martinez,Vesa Jylhä-Ollila,Andrés Ramírez Pérez2,Alex Steenbruggen,Dave Cameron,Andrew Kerr3,Sanjin Delalic,Yusuke Nodake,Mark Kerr2,William Tahran,Craig Deshon,Jason Schiwy,Jonathan Tussey2,Johnson JW Yong,Nicolas Camacho,Connor Parise,Matt Koerner,Cooper Collier,Patrick Ramirez,Daniel Kaps,Rodrigo Meezs,Michael Keymont,Daniel Serrano Rayo,Jackson Freer,Joe Marlin,Christophe Fuchs,Chris Kierce,Scott Myers,David Garrido Sanchez,Tom Ruff,Carlos Andres Medrano,Sebastiaan Neefjes4,Marco Corti,Rod Dagneau,A Henderson,Aitor Figal Fernandez2,Jose Antonio Yañez Buron,Siim Loog,Anton Oud,Salvador Jimenez,Ben Clemson,Yuuya Tanaka,Aiden pyke,Arthur Rymer,Bruno Tassone,Nathan Dudek,Andrew Trimbach,Jose Luis Paz Vilas,Jacob Smith2,Adam Facciponti,Daniel Nogueras,Jack Laidlaw,Jef De Haes,Kai Uwe Gerlach,Javier Alvado Moll,Matt Fretwell,Taiki Yamaguchi3,Gregory E White,Jose Conde Ortega,Steve Smyth,Adolfo Martinez Vazquez2,Sam Dunstall,Rick Hansen2,Ivan Prendes,Karl Thomas Daum,Alto Dykes,Alex Ward,John Flowers,Xavier González,Gerald Chevalier,';
$formula_renault2 = 'Aleksandr Potapov,Colin Gregory,Robert Jones10,Alex J Krejcie,Ryan Arroyo,Samuel Steffy,Carlos Fonseca,Antoine Thisdale,Diidier Lapchin,Alistair Hay,Ivan Hernandez,Jiri Mojak,Stephan Roesgen,JC Tussey,Jean-Francois Godin,Alex Thornton,Miguel Invernon de Julian,Thomas Ligon,Ryan Ligon,Lee JR Williams,Christoph Aymon,Matthew Wilson4,Abhinav Thakur,Tom Depke,Steffen Herrmann,Claude Lessard,Chris Knight,Harry Cowan,Benjamin Morse2,Daniel Felix,Tobias Beckmann,Ziv Sade,Rene Lopez,Rian Moore,Scott McIntyre,Frank Rask,Lionel Gamet,Kristian Takacs,Samuli Strang,Dmitri Janis,DariuszD Szymecki,Fabrizio Raffa,Roel Frissen,Renat Satdarov,Jacques Richard,Wei Han Chan,Jeronimo Mosquera,Jeff Sparks,Duane McCarthy,Steve Honan,Gabriel Borghi,Isaac García Domínguez,Alex Fedurco,Mark Flowers,Greg Waters,Geoff Killick,Bart Vandenryt,David Araque,Marcus Simonsson,Lucas Navarrete,Tristan Koch,Scott Garner,Nick Kallinosis,Koichi Wakiyama,Adam Perkins,Michael Hilliard,Thomas Jordan,Ronny Granzow,Shayne Allen,Tyson Meier,Jason Lundy,Aecio Telles,John Gomes,Sebastian Peter Schaar,Alexander Porcelli,Roger Jackman,Michael Knapp,Tyler Lugo-Vickery,Alexander Przewozny,Aleksandr Drozdov,Brenden Baker,Marcin Gadzinski,Oliver Augst,Christophe Brengard,Marc Schulz,Anthony Burroughs,Marty Vrana,Kevin R Jowett,Julien Dauber,Miguel Cela,Jenno Buelders,Jose Gomez2,Robert Fletcher2,Dean Oppermann,Thomas Ingram,Gary Weaver,Joshua Stuart,Aaron Upp2,Daniel martin leite,Jason Walat,James Webb6,I.M Keeman,Liam Gordon,Josh Hall,Mitsuhiro Kozai3,Christian Van Egmond,Davy Schaepdryver,Gavin Simpson,Jonathan Leighton,Darius Trinka,Jose Carlos Campodonico,Daniel Willits,Jonathan Perl-Garrido,Xhulio Zhonga3,';
$indycar2 = 'Adam Blocker,Cameron MacPherson,Andre Carlos Smith de Vasconcellos,Julien Flouret,Riku Roiha,Cesare DiEmidio,Matt Miller,Robert Obrohta,Randy Shewmake,Anthony Obrohta,Robert Crouch,Joel Tremblay,Tamas Rekettyei,Mark Bartholomew,Fabian Kloth,Roman Belogorodov,Markus Niskanen,Cristiano Benevenuto,Henrik W Nielsen,Cristian Camozzi,Toni Andrade,Mark Prince,Rafael Davila,kiko Dominguez,Daniel Vieites,guy TESSORE,Adam Webb2,Craig Hobson,Ryan P Andrews,Sean Disbro2,Justin Morton,Brandon Kilgour,Ilya Babansky,Zachary Sears,Jeffrey Oakley,Philippe Lambert,Robert Lewis3,Michael Peters,Sergio Madrid Hernandez,Markus Kuttelwascher,TM Hauser,Joshua Chin,Daniele Noventa,Roy Ricklin,Paul Morris,Brinton Hockenberry,Brian Cross,Richard McClure,Doug Lierle,Victor Diprizio,Brian Zager,Irmas Ibric,Robert Collins,Evan Black,Josh Lewis,Alessandro Dalledonne,';
$indycar3 = 'Jeffrey Koolbergen,Taylor Cox,Jorge Destro,Vincent Brehm,Joe Marks,Andrew Botterill,Johan van den Beld,Tyler Macon,Jerrad Daniels,Ray Kingsbury,Steven Wilson3,Jay M Lawrence,Sergio Corral Capitán,Tom Hunt,Carter Kundinger,Borja Orizaola Alonso,Paulo Ferrari,Daniel Knight3,Matt A Kingsbury,Neffry Aawg,Jonathan Morrison2,Adam Dock,Marcellus Breuning,Bill Krause,Gilles Leblanc3,Juho Raina,Mark Ussher,Daniel Colgate,Roberto Giudice,Leandro Coelho,Andy Cocilova,William Casey,Benoit Savoie,Caique Ribeiro,Pierre P Melanson,Michael Rossow2,Ernie Ludwig,Michael Melvin,Miguel Villalobos,Daniel Plamondon,';
$promazda_2017_12 = 'Alessandro Maresta,Takeo Goda,Antti Viisas,Scott Dick,Fernando Jaquez,Ricco Shlaimoun,Christopher Shelton2,J-René Perron,Leandro Bignardi,Jose Antonio Camara Paredes,Jim Lakey,Massimiliano Cherobin,Jorge López Expósito,Rod Potgieter,David Sanchez Sanchez2,Daile Taylor,Andreas Nilsson,Martijn Arnoldus,Emmanuel Suter,Anne Medema,Rich Reybok,Robert Burton,Greg Alexander,Michael La Rosa,Dudulle Sanchez,Christian Mozas,Andrew-Adair Saunders,Thomas Dawson,Ilya Yevstrin,David Dendelot,Karl Crowley,Cheuk Lai Ho,Rod Fausnaugh,Daniel Balogh,Derek Fox,Ronald Mcmanus,Ryan Cullan,Angel L. Lahoz,PJ Salley,Craig Ridley,Jose Maria Peñalba,Michel Prudhomme,Jose C Palacios,Henry Robert,Roel Geuze,Davide Villa,Owen A Morgan,Dominik Gerardts,Sergio Sanchez Vilar,Jos van de Ven,Clemens Modl,Juan Zamora,Daniel Lerchner,Jean-Michel Noyon,Michael Dix,Don Warrenburg,Ransom Cubitt,Angelo Eduardo Pereira,Sebastien Proulx,John Mchutchison,Ignacio Tessari,Xavier Descarpentries,Colin Kaminsky,Joachim Politzer,Ken Soszka,Jerry Notowitz,Achim Ennenbach,Shawn Romig,Coleman Reif,Brad Bray,Daniel Quattropani,Ron Wallace,Mario Tessier,Chris Heideman,Donal Fitterer,Luuk Kikkert,Antonio Martinez,Vlad Zayonchkovsky,Athanasios Pellas,Vladimir Reva,Richard Hnatyk,Arthur Plumridge,Walter Giacopuzzi,Glenn Dyson,Izael Castro2,Adolfo Corrales,Gerry McAreavey,Jake Feinerman,Steven Carkner,Dmitry Ivanov,Yoann Seiller,Kieron Fishlock,Alessandro Gaddi,JF Godin,Med Druelle,Kyle B. Tucker,Rod Acevedo,Scott Gruber,Everett Paddock,Russ Addie,Flávio Saturnino,Mathias Lea,Dale Groth,Jeffrey Stamp,Lance Gardner,Edgar Tutwiler,Graeme Thomson,Eduardo Italiani,Jaime Duque C,Andreas Ivo,Marco Oliveira2,Derek Feuerborn,Mathias Merlano,Douglas Woodside,Petteri Ruotsalainen,Joey Vergara,Gabriel Pérez,Vincenzo Carchedi,Eric Lidvall,Francois Gagne,Joe Renn,Aurélien Gimlewicz,Arthur Chan,John D Cannon,Richard Wedsted,Garth Gastmeier,Paul E Thompson,Donivan McKenzie,Alejandro Portugal,Timothy McFarland,Earl Setser,Brian Chatfield,Ryan Cornes,Ari Cejas,Thomas Dudek,Dinyo Dragiev,Joseph Dawisha,Milos Práchenský,Steven Denton,Gary jay,Matt J Anderson,Jens Windeler,Hayden Gober,Orest Ludwig,Paolo Bonasera,Jeremy Naylor,Charlie Ryan,William Webb,Gabriel Wood,Benjamin Morton,Joshua Kotten,Chris Wetz,Kris Walker3,Ben Voellinger,Manu Rey O´Largo,Michael Stanley,Uldis Puteklis,Javi Torres,Jeff Simon,Stephen Jones5,Kevin Cline,Luis Garzon,John Toms,Julien SOUTADE,Stanislav Shcherbak,Jonathan Noland,Julien Orgeas,Vincenzo Taormina,Juan Pablo Rodríguez,Jeroen van Wermeskerken,Igor Bahillo,Daniel Shoup,Matthieu Bouthors,Malte Stahnke,Danilo Sanfilippo2,Matthias Huebl,Arne Stops,Steve Jenks,Cristobal Gutierrez,Lorenzo Bertini,Bryce Weir,Mariano Martin Garcia,Christoph Leister,Oliver Meyer2,Bill Williams,Erich Smith,Justin Bullard,Claudio Ferrera,Charles Woodward,Bryan Lotshaw,Matthieu Vincenti,Corey Wharton,Emil Orza,Neil Scott2,Lauri Mäkitalo,Julian Dunne,Jaume Soler,Luigi Gaggiano,Jose M. Sicilia Sanchez,Sebastian Dahlmann,Scott Holmes,Marco Samson,Chris Foy,Chris Kneifel,Alexander Ruett,Ronnie Sewell,Plinio Ferreira,Michael Cotchin,Justin Estes,Max Ainscough,Bernd Schmidt2,Andrew Carr2,Joseph Newman,Michael Wojciechowski,Enrique Cruz,Emanuel Lopez,Luis de la Ossa,Igor Arkhangelsky,David Bradley5,Arda Yayla,Tom Wedgwood,Pablo Bernal2,Thomas Krug,Robert Roest,José Luis Jiménez,Fabio De Prisco,Kristiaan Ritsema,Matthew Overton,Mickey Kern,Daniel Mattos Lobao2,Edward Gorman,Adam J McLeod,Greg Bernardo,Igor Gazzani,Graeme Burnett,Ryan Verhulst,Luis Zambelli,Yili Xia,Michael Parkhurst,Jari Kuokkanen,Coen Evans,Daniel Stokes,Carsten Knoll,Andrew Jones2,Stephane Balouka,Christian Gonzalez Marugan,Jeff Secord,Jay Dargert,Jack Atkinson,Yusaku Shimizu,Claudio Henriquez,Gabriel Ramos2,Aris Faria,Fernando Marrero,Biemar Ludovic,Michael Jeanes,Adrián Antón García,Alejandro Trenor,Jay Freels,Markus Schmeinta,Kasper Petri,David Lujan,Stefan Mager,Dominik Färber,Hugo Galaz,Ragonneaud Jonathan,Sergio Martínez,Davy Jasmin,William Whalen,Timothy McDonnell,Christopher Mellor,Martin Falz2,Takeshi Kita,Xabier Sanchez,Alain Robles,Gianni Raspaldo,';
$promazda_2017_12_2 = 'Rob Crouch,Tommie Aalto,Sergio Quero,Takashi Aoki,Markku Laakkonen,Javier Oyarbide Apalategui,Wilhelm Öhman,Sascha Kuss,Ian Sweeney,Michael A. Overbay,Keith Bridgman,Andre Van Bijsterveld,Tomás Agustín Lanús,Tom Paine,Jim Day,austin Snyder,Estruch Frederic,Pontus Nilsson,James Mcritchie,Sergio Garcia6,Scott Kukas,Jorge Vaquero,Warren Wilcock,Karun Nadarajah,Paulo Lopes De Amorim,Jacob Honekamp,Pierre-Benoit Lemire,Bruno Caldas,Jose Martinez8,Leandro Craveiro,Scott Spidle,Greg Coldwell,Mark Clavell,Ad Tiegelaar2,Sean Hottois,Matthew Freeman2,Richard Allen5,Tamas szene,Ronald Overbay,Peter Barcsa,Brian Blackburn,Marco Ricci,Serg Surovikin,Amin Guindi,Randy Freeman,Matt Fisher,Ismael Hernández,Wayne Whiting,Patrick McKenzie2,Luigi Monticelli,David Alexis Jordan,Jeremy Richey,Andrea Borri,Hrvoje Mihajlic,Stephen Crawford,Gregory Brown,Christian Iglesias,Alexandre Baril Lagace,Mitchel West,Istvan Fodor,Zachary Leman,Dane Knezic,Dwight Sontag,Matthew R Mills,Victor Mayorga,Bruno Vitorino,Felipe Calderon,Charly Fruehwirth,Adrià Povedano,Ismael Martin Garcia,Thomas Tlusty,John Hamblin,';
$indy_road_s1_2018 = 'David Soranzo,Andrew Z Wood,Matteo Ugolotti,Thomas Ellison,Alex Albert,Juan Gines Gonzalez Rodriguez,Matthew Burroughs,Jackson Robillard,Fernando Núñez Correas,Svetlana Dorokhova,Toni Crevin,Martijn Vosseberg,Adrian Rodriguez5,Scott Bolster,Dominik Lukomski,Travis Handleson,Michele Ricci,Djawad Karoni,Michael Derby,Kyle Smith10,Brian Gavin,Andrew Fawcett,James Coulibaly,Santiago J Rodriguez,Andrés Mora,Matt Roseland,Thomas Axsom,Justin Hatin,Ian Quass,Juan Ignacio Jodurcha,Gregor Verstraelen,Mike Lemon,Jorge Garrido,Ailton Andrade,David Osolin,Joshua Gardiner,Thiago Peres,Antony Woodward,Charles Boyd,Justin Hall,Travis Floyd,Jeff Tyler,Jack Godden,Michael R Weber,Oscar Hjelm,Jagger Jones,Ryan Hoffman2,';
$formula_renault_s1_2018_r1 = 'Gines Carvajal,Juan Antonio González Díaz,Antonio Trueba Buenaga,Ivan Jovic,Luciano C Pereira,Michael Brunkhorst2,Matthieu Chauvière,Fabian van Dooren,Jason S Dyer,Kip Barrett,Miguel Angel Muñoz Juarez,Terry Silvers,James Burn2,Tino Stolze,Albert Chasco,Pablo Collado Andreu,Luis Mendez,Antonio Iglesias Osuna,Richard Lishman,Thierry Bazi,Myles Dixon,Marc Estruch,Stephane Thallinger,Yohann Harth,Leonard O Cheri,Jan Brezina,David Antequera,Robert De-Souza,Stefano Carlo Finazzi,Erik Buter,Craig Hardy,Marcus R Taynton,Lucas Bahl,Ibrahim Anem,Michiel Oom,Jorge Granda,Cedric Colleau,Jose Morales Lorente,Joshua Lad,Dries Vandenryt3,Ralf Deininger,Nuno Pereira Tang2,Gleison Santos,Eugene Wilkinson,Jonathan Dickert,Breno Fachini,Mattias Bengtsson,Nicolas Stella,Josh Wheeler2,Yannick Ongena,Mark Moser,Victor Mostaza,Mariano Hernandez Marinas,Ariel Guerra JR,Gary Wells,Casper van Zon,Tom Dreiling,Aaron Hackney,Martin Ferreira,Patrick R Kessler,Justin Hess,Gerard Cabrera,Westley Scott,Sergey Glazkov,Lee Harding,Victor Bukovetsky,Tim Bergmann,Pedro Gomez,Kenneth Kjær,Daniel Diaz Escobar,Roberto Stefano Kovac,David Santana Herrera,Guillermo Marquez Alvarez,Carlos Rosdevall Carrasco,Nikolay Vasilyev,Lee Allen,Charlie Summers,Robert Dutton,Vincent Guertin,Vincenzo Marchiafava,Peter Dimov,Min Fernandez,Diego R Alonso,Axel Chrétien,Wouter Remmerswaal,Marcos Aroztegui,Joe Beasley,Luiz Felipe Tavares,Jason Baird,Thierry Daveine,Xavier Naud,Sergio Tapia,J Carlos Villarejo,Rudolf Nagel,Brian Williams,Remigio Di Pasqua,Josh Conover,Glenn Bonnet3,Woody Mahan,Robin Steffers,Vern Nobles,Derek Macdonald,Thomas Andriamiharisoa,Jeremy Monchablon,Frederic Sbardellini,Ernesto Bueno,Christopher Block,Nathan Smith4,Robby Foley,M Diaz Costales,Bruno Dupont,Stephane Boyer,Luis Velazquez,Mikel Palmer,Benjamin Brenot,Lionel Oberto,Samuel Harris,J.J. Tubb,Mark Kedrowski,Aurélien Talmon,Gerard van Langevelde,Albert de Neeling,Antonio Urbano Checa,Greg King2,Nathaniel Rudser,Rico Rehfeld,Edward Raprager,Laurens de Rijk,Carlos Fernandez Espinosa2,Daniel Fletcher,Alejandro Puga,Tuomo Seppälä,Santiago Carducci,Bryan Smith4,Jason Stout,Toni J. Garcia,Thomas Seichter,James Loukota,André Machado Domingues,James Dowling,Luke Barton,Antonio Vidal Jr,Amir Farid Abdul Gani,Sebastian Roldan,Gerard Ocariz,Nacho Casas,Cedric Finsac,Roel Houben,Jonathan Almenar,Mario Pesce,Jared Turnbull,John Mcconville,Yohann Margot,Ryan Littlemore,Knut Martinsen,David Catling,Oskar Fernandez Arjones,Mark Hertzog,Michal D Brnak,Nick Carroll,Carlos García Ramos de Lorenzo Cáceres,Florian Denard,Pierre-Olivier Valette,Said Reklaoui,Martin Brnak,Jose Miguel Galilea,Barry Baird,Dan Lathan,Antonio Miguel Ovies Ferro,Miguel Antolin,Simon Underhill,Joerg Mani,Mateo Couso,Jason Dilworth,Sasha Varga,Rick Barrow,Mick Carr,Jan Steltenpool,James Watson7,Sinan Seninan,Yannick  Lapchin,Michael Gene Nelson,Dan Sanger,Ari Kesseli,Justin Kidd,Victor Del Valle,Dallas Pataska,Leandro Mendes,Thomas Leathley,Ignacio Senao Sanchez,Mariano Sancho,Brent Bartholomew,Tony Baird,Yaminel Diaz,Miguel Angel Perez Fernandez,Alex Ayerbe Echeveste,Manu Heikkilä,Oliver Kemmesat,Julian Oejen,Hugo Queniat,Julian Orozco,';
$formula_renault_s1_2018_r2_to_4 = 'Jon Harrison,Rominho Soares,Donovan Waldenmyer,David Gimeno Bou,Abdulkheir Al Amry,Jordi Angrill Riera,Angelo De Cillis,Andres Marquez Garcia,Ricardo Neto,Jeff Heeney,Dennis Chuprina,Jose Manuel Sojo Oria,Andre Juteau,Roderic Kreunen,Andras Laszlo Kovacs,Matthew Maslen,Maikel Rincon,Owen Martell,Aleksi Heinonen,Josh Thompson,Aedan Campbell,Deni Raets,Ben Prior,Ivan L Garcia,Rusty L Kruger,Brett Dal Santo,Jens Hartvig,Cristobal Valenzuela,Kevin Cochran,Noriyuki Negishi,Pat Gallo,Pasquale Iannucci,Philip Schiller,Bruce Gallaway,Alvaro Sanchez4,Jason Boak,Mike Cannon,Christophe Vandeputte,Mickael Etienne,Brandon Ebright,Nelson Verdier,Michael Scurlock,Oscar Ruiz,Víctor Abellán,David Justo,Jorge Carracedo,John Ha,Ángel Salas,Ulises Valido Gonzalez,Ruben Gomez.A,Vincent Revel,Martijn Versleeuwen,Gary Charles Taylor,Stephan Chantal,Oier Jugo,Michael Edens,Rafael Serna,Sergio Almela Jr,Christopher Kempa,Pedro Jose Luis Buyones,Sergio Rincon,Steve Bammer,S Naser Teymourian,Michael Schlegelmilch,Govand Keanie,Kyle Stewart,Bradley Sikorski,James Beardsley,Tomi Nousiainen,Thomas Lackner,Edoardo Bruschini,Kris Kornder,Magnus Vallström,Ricardo Peña Alonso3,Daniel Delgado4,Gil Esteves,Eder Belone,Markus B Bausdorf,Lars van Slooten,Jordan T Vlasnik,David Rodriguez4,Kenneth Skytthe,John Hurley,Jose Cabanes Martinez,Bradley Hyra,Erik Johansson3,Sergio Morales,Sergey Soldatov,Tommy Nilsson,Thornton Muir,Ainsley Martin,Patrick Søby,Joachim Jaderyd,Hector PereiraB,Paul Carter3,Bradley Collier-Brown,Stan Hindman,Israel Marín,Noah Watt,Davide Porceddu,Mark Shelton,Asen Doykin,Thomas Witrahm,Richard ARNAUD,Oliver Berger,Matt Million,Lucas Baweda,Roberto Alvarez,Kevin Edge,Christian Krogh,Pedro Santiago Soler Garcia,Jamie Besaw,Grayson Bedolli,Frederik Vesti,Oren Sadeh,Joeri Steinmetz,Daniel Campos,Trevor Johnson,Cesar Armengod,Edgar Montelo,James Flynn,Antonino Arcidiacono,Jonathan Beikoff,Maxime Cathala,Anthony Wynne,Kim Koch,Daniel Buck,Mansuet Grasser,Clive Norton,Marko Panger,Adam Thieme,James Houghton,Greg Prince,Laszlo Morocz,Riaan Lourens,Bruno Berg,Daniel Perry,Wilfried Dansicare2,Keith Kovac,Mark Anthony Taylor,Alejandro Sio,Steve Clay,Richard Kovacs,Richard Hearn,Marc Manius,Anthony Manes,John Langan,Carl Wicker,Dennis Nicoll,Diego Pesado,René Osterkamp,Karolis Jovaisa2,Laurynas Vaitiekunas,Aaron Sutton,Oliver Elliott,Alex Laidlaw,Xavi Navarro,Josello Lopez,Jakob Berthelsen,Peter Boege,Sergio Gurdiel Ferreiro2,Tom Egan,Eduardo De Carvalho,William Power,Pierre Fabre,Jochen Rinck,Adam Hadfield,Yoni Lunelli,Klaus Vörding,Olli Kwoka,Gerben Kelly,Roger Yoke,Klaus Beulen,Nick Rowland,Christopher DeHay,Vladimir Yurkin,Germano Cervini,Rob Hopp,Ricardo Verdasca,William Moody2,Marcello Bonacossa,Gregor Kinski,Álvaro Robles,Dominik Gätjens,Francisco Matias Molina Jur,Jeffrey Field,David Rey,David Hornillos Sánchez,Sam Devantier,Florian Decker,Tom van Doorn,Jose Francisco,Kasper Thrane,John Boy,Marcus Dunsby,Timo Pennock,Stjepan Novosel,Barry Miller,Gordon Anderson,Richard Bakker,Paul Stephenson,Jose Gavilan,Alex Scribner,Federico Fornaro,Johannes Vahala,Niclas Gustafsson,Jim Leeseman,Christoph Holstein,Manuel Palacios,Leo Lazcano,Jack Bell,Nate Lundy,Alan Stover,Frank Voerding,Joe Normile,Clement Gonzalez,Lasse Sorensen,Ralph Bölsterli,Tanguy Pedrazzoli2,Marcelo Mandaji,';

// Also invited to Undie Lights
$dallara_dash_s1_2018_w9 = 'Russell Dow,Ryan MacPhail,Jonathan Dekuysscher,Russ Thompson,Tanner Litowski,Sergio Franco,Sergio Moyano2,Leonardo B Campos,Csaba Csikasz,Jan W. Roestenskar,Rafael Sempruch,Christopher Bouchard,William Hull,Martin Brown,Kolja Birkenfeld,Stephan Hornig,Lari Salminen,Kurt Müller,Jeremiah Smith2,George Oliver,Jim Henley,James Brant,Sam Hazim,Finian Dcunha,Brandon Jenkins,Stephen Boot,Michael Charles,Richard Mason,Matthew McKinney,David Fish,Kris Baron,Brett Weatherill,Alex Imhoff,Joao Milani,';
$dallara_dash_s1_2018_w6 = 'Jay Plummer,Henri Zimmermann,Joseph Yeager,Jason Rogers2,Matthew Fritz,Russell Dow,Ryan MacPhail,Jonathan Dekuysscher,Ryan A Plourde,Russ Thompson,Nicolas Laurito,James Texeira,Tanner Litowski,Garrett Dix,Sergio Franco,Sergio Moyano2,Christopher Kehrer,Leonardo B Campos,Csaba Csikasz,Curtis Knowles,Thibaut Lefevre,Olivier Vinot,Jan W. Roestenskar,Warren King,Rafael Sempruch,Christopher Bouchard,Vasily Slabkov,William Hull,Tyler Peck,Martin Brown,Xavier Added,Kolja Birkenfeld,Andrew Parker3,Stephan Hornig,Lari Salminen,Kurt Müller,Jeremiah Smith2,George Oliver,Jim Henley,James Brant,Sam Hazim,Finian Dcunha,Brandon Jenkins,Dezso Kovi,Stephen Boot,Alex Ripstein,Michael Charles,Richard Mason,Matthew McKinney,Kyle Books,Logan Seavey,Spencer Patterson,Maródi Sándor,David Fish,Kris Baron,Brett Weatherill,Kerry Edwards,Alex Imhoff,Doug Adams,Jayson Conover,Joao Milani,David Haines,Alberto Seijo Ramos5,Thomas Schmitz2,Pablo Hernandez,Devin Terpstra,Jason Frisk,Edin Frzina,John White9,Michael Copeman,Fred Desaix,Michael Simons II,Michael Ducker,Andrew Thomas4,Alexander Stark,Matthew Laventure,Roger Homann,Zach Wasson,Derrick Jackson,Luis Albert,Jason Grooms,Daniel Aish,Koen Attard,Michael D Hoefer,Graham Ritter,Luis Fernandez,Arie Brooshoofd,Chris Bourne,Ryan Broderick,Wesley Pistole,Iván Casado Mouteira,David Peck2,Christopher Potter,Andrew Welter,David Raber,Jim Reed,Joe Mazziliano Jr,Khaldoon Waleed,Brandon Chappell2,Gary Hammann,Bradley Matheson,Matt Kemp,Pete Bent,Guilherme B Farias,Fred Hazelton,Simon Moseby,Tiago Ribeiro,Charles Rutter,Steve Cassel,Pedro Nogaroto,Victor Brill,Rob Marona,Timothy Huffner,Miguel Oliveira,Ryan Renouf3,David Delgado,Chris Baedorf,Joel Epperson,Jarbas Dal Lago,Covy Moore,Marco Ravaioli,Nick DeGroot,Kevin R Lambert,Chris Bradick,Israel Oliveira,A.J. Smith,Andrew Marsh,Hollis VanderLoon,Patrick Große2,Nico Große,Samuel Prince,Alain Apers,Jeffery Vandervort,Michael Garrant,Chet Milensky,Nicholas Donovan,Alex Simonic,David Hebert,Jason Stehney,Benjamin Perez,Matt L Cole,Scott Peterson,Ryley Downey,Romain Delamarre,Dwight Plesh,Flavio Clivati,Kristjan Aasmäe,Jarek Marcin Kolman,Clemens Berg,Mark Patterson,John Cope,Bayram Kiyak,Tony Magliocco,Nicholas Borgetti,Dev Gore,Valentin Fettig,Romain Bouteiller,Nicolas Pillon,Seth Nixon,Wyatt Foster,Robert F Kenny,Adam Hall,Bobby Sweeney2,Vincent Scatena,Tobias Röhner,Charles Baccio,Steve Beattie,Matthieu DESCARPENTRIES,Michael Schmitt,Raul Miranda,Scott Smith,Marc Dolores2,';
$indy_fixed_s1_w1_w9 = 'Frank Oosterhuis,Jim Glennan,Daniel Avery,Jose Wilcocks,Evandro Arcega,Marcelo Zanatta,Sylvain Moraine,Edward Lack,Gerald Carrell,Gregg Trego,Nick Nichols,Loren Arden,Jean-michel SANCHEZ2,Loic Barbe,Alberto Carmona Saiz,Henry Holder,Rashad Craig,Michael Gonzales,Xavi Iborra,DV Warhurst,Jack van Hees,Rock Harris,Pepe Recio,William Kabela,Ray Griffioen,Marco Wust,Andrea Karina Ciminelli,Mark J Edmonds,Reinhard Sandtner,Roberto Pancaldi,Michel LE HAZIFF,Hunter Smith,Bono Huis,Harold Palmer,Pascal Dupas,Giovanni Coronin,Tobias Seigner,Marijn Thielen,Raphael R Sabara,Jorge De Celis,Thomas Lademann,Alain Tessier,Gabor Kaloczi,Miguel Angel3,Dustin Hickmann,Christoph Weitz,Chris Zijlstra,Stephen Lee2,Jürgen Frank,James Gerity,Emmanuel Galinier,David THOMAS8,Carl E Jansson,Alfred E Shepperd,Thomas Siclari,James Rawson,Brandon Carl,';
$from_own_indy_fixed = 'Ed Burnett,Bryce Ring,Graham Bunyan,';
$indy_fixed_iowa_tuesdays = 'Richie Pittenger,Kevin Bello,Luis Area,Walter Haslauer,Tony Pizzaro,Marcel Gagne,Terry Siders,Fred Phillips3,Adam Meier,Samuel Roth,Craig Fuller,Josh Stetz,James Winteringham,Jean-Marc Drevon,Jan Schwitter,Michael Thomas3,Cory Donahue,Thierry Le Gall,Vax Bourgeois,Mike Grimshaw,Andrew Hemsley,Neil Archbold,Tom Brakel,Karan Saxena,Mathew Rise,Hector Zanardo,Marco Barbanera,Dave Walsh,Mark Hesso2,Ivano Spigariol,Adam Piszczek,Tony Althoff,Matthias Willhardt,Michael Duforest,David Johnson16,Marcel Boersma,Juri Jerg,Scott Carpenter3,João Martins,Alex López,Mauro Belloli,Enzo Fazzi,John Morris7,Manuel Domingo,Patrick Quispel,Olivier Pouteau,Julien Alavoine,Thomas Stockmans,Jorge R Navarro,Matthias Riedel,Marcel Ortstadt,Emerson Nogueira,Peter Brennan2,Joffrey Laberche,Walter Macias,John Godfrey3,billy Brakel,Fernando Velayos,Jesus Martin Sanchez,Javier Cervera Rivas,Michael DallaValle,Dylan Urtubey,Márcio de França,Patrick Fleischer,Stephen Schumph,Carlos Gomis Sánchez,Yannik Danisch,Chad Osborn,David Williamson2,Rory Folsom,Thierry DEGEILH,Thomas Wigent,';
$skippies_s1_tuesdays = 'Luis Miguel Diaz,Miguel Ángel Aranda Martínez,Chris Carvalho,Christopher Robinson,Ken Ladd,Christian Rodríguez,Alberto Ruiz Sobrino,Carlos Via Dufresne Ley,Davide Righini,Ricardo Carrillo,Jaime E. Nebot,Chema Ferrero,Larry MacLeod,Tom van der Voort2,Mark Perez,Jose Carlos Navarro Durango,Brandon Hawkin,Mathieu Lesly,Hugo Neto,Alvaro P Sanchez,Rafael Marquez,Fernando Sacacia González,Michael Owen,Michel Lambinus,Scott Bates,Erol KOROGLU,Anton Mesi,Steven Nelson Jr,Israel Alonso,Echedei Benitez,Adrián Chamorro2,Pietro Marchisella,Miguel García,Francisco Javier Sanchez Lopez,Nicholas McDevitt,Richard Fleenor,Perry Newhook,Renan Azeredo,Sebastian Scholz,Jokin Castaño Rodrigo,Romain Jaureguy,Samuel Fernandez,Beñat Taberna Telletxea,Peter Cowan,Ian Baker,Aarón González,Philip Finster,Ricky Proffer,Randy Hough,Roberto Suarez Rodriguez2,Roman Alvarez,Marcus Burkitt,Frank Ahlgrimm,Sebastian Karlsson,David Martínez3,Stefan Tscherne,Geoffrey Bachelot,Alex Zuloaga,Tomas Rajchman,Victor Ramirez Lopez,Ivan Sanchez Gutierrez2,Bastian Huber,Stuart Atkinson,Kacper Kolodziejczyk,Luiz Garcia3,Mark Bresnan,Vincent Moriere,Svein Tore Duaas,Andrew Hughson,Oskar Solé,Roel ter Maat2,Teemu Suhonen,Pedro García Alcalde,Carlos Alberto Quijano,Neftali Montesinos,Ari Collado,Maxi Jarruz,Rafa Serna,Simon Abitbol2,Fabien Vallet,Moritz Fricke,Andreas Dahlström,Tommi Kallio-Kujala,Ander Marro Allende,Alvaro Robles,Carmine Sorrentino2,Christopher Rabey,Kamil Poleszczuk2,Peder Blomkvist,Mihail Vasilescu,Dieter De Ridder,Matthew Paynter,Howard Joseph,Lewis Ward,Kevin Oonk2,Nicky Dekker,Tyla keveth,Charles Kellyman,Burke Treidler,Daniel Sharp,Bill Fraser,James Childe,Kevin Croswhite,Daniel Garrison Jr,Nicki Thiim,Till Stoecker,Emil Sjöblom2,Andreas Hammerbach,Joseph Maggio,Hubert N Dollen,Fredo Große,Derek Jones3,Jan Ole Bendiksen,Mike Girenz,Jean-Paul Lanaux,Ole-Marius Jensen,Oscar Zappaterra,Mike Bucher,Andrew Hasler,Andre Rajkovic,Florian Bauernhofer,Bastian Graber,Daniel Vaca Araujo,Robert Barnes4,Jacques Plourde3,Daniel M Bell,Yuri Gomes Soares,Fabrizio Ugolotti2,Shaun Dunbavin2,Javier Álvarez Lago,Kevin Botelho,Ryusuke Masumoto,Jamie Thomas3,Jonas Sørnes,Brian Murphy3,Paul Rowell,Zlatko Knezevic,Juan A. Escobar,William Marshall,Daniel Nadal Mayorgas,Sergio Aránega,Brady Fisher2,Michael S Engler,Paulo Mourato,Alexander Spring,Jeremy Boucher,Pablo LLoves,G J Menendez,Sonny Hansen,Michael Bittmann,Andrew Lane2,Akihiro Hagiwara,Salvador López Cascant,Pascual Blas Millan Esteller,Jonny Neilly,Vicente Herrero Vicent,Pascal Martineau2,Johnny Labay,Thomas Kauffman,Gerrard Daly,John A Sheehan,Jeromy Hessels,Fabio Berchtold,Zsolt Szaszak,João Carriço,A.J. Roper,Benjamin J Szoko,Anderson M. Ramos,Jakob Lehmann,Axel Begemann,James South,Carlos Blanco Lafuente,Diego Ortiz Diaz,Tony Jacobs,Miquel Ramos,Alex Otero,Ben Luker,Beau Dixon,Zlatko Ivankovi?,Marie-Helene Bredeaux,Josh Brain,Alexis Tapia Vico,Josemi Chaves,Huseyin Dagli,J. Javier Muñoz Dominguez,Laurie Britt,Pol Morales,Kenneth Lundkaer,Henrique Hesen,Chris Radisich,Oscar Escudero2,Jorge González,Carlos Urrea Sainz,Toni Vallejo Caballo,Alberto Gomez,Stephen Mc Caffrey,Daniel Kükenbrink,Adrian Ramirez Wrobel,Joshua Cartwright,Gregory Denys,Enrique Jose Fonfria Pardo,Roger Auerbach,Roberto Biensoba Cala2,Patrick Brunias,Lukasz Krajewski,Joel Meozzi,Don Stephenson,Samuel Dick,Fabrice Awuitoh,Tom Van de Pol,Jesus Hurtado Recio,Perry Warburton,Daniel Pezuela Garcia,Dale Pedersen,Javier Pérez Loscos,Washington Luiz,Jordy Lopez Jr,Tomas Chacon,Aurélien Djeauz,Francisco Corredera,John Izzo,Raul M Blanco,Kip Dent,Juergen Mitterlehner,Alexey Sirotkin,Gary Thompson,';
$ruf_s2_2018 = 'Uwe Länger,Grant Henderson2,Raul Monraba,Alcides Dias,Roberto Simonetta,Ed Rest,Alberto Veiga,Joerg Heimbach,Paul Armstrong2,Jack Keithley,Tom Michelmore,Javier Ramajao,Olivier Vignone,Giuseppe Arena4,Jerry Skarbek,Ruben Olivares,Magnus Asp,Diego Suarez,Felipe Javier,Paul A Nelson,Marc Gurri Sacristan,Daniel Orban,Robert Rippl,Oscar Mardones,Javi Martin,David Ogden,David Gil3,Franco Zegatti2,Thomas Silbernagel,Raffaele Gammone,David Meneses Martinez,Wolfgang Schnalzger,Gerardo Babio Ramos,Marcel Huwyler,Jairo Bernal,Tommy Mitchell,Dave Kirk,Gilberto Miglioranza,Thomas Orthoff,Jose Antonio Fernandez Rubio,Javier Lázaro,Vincenzo Pariante,Rik Sneyers,Esmeralda Rodriguez2,Fernando Couto,Ludovic Valarcher,Timo Heyden,Jesse Lynch,Angel Castejon Antuña,Francisco Ruiz Martinez2,Kyle Long3,Alejandro Inestal,Claudio Colucci,Pawel Sobocinski,Mindaugas Kezys,Jose Pinho,Sebastian Reeh,Aleksi Aalto,Maurizio Cheli,Ziad El-Mustafa,Jason Dunnington,Oleg Varfolomeev,Miguel López-Torres Stadler,Eric Hanna2,Lynn Wilson2,Lewis Bussa III,Maurizio Costanzo,Chinmai Kristo Punukollu,Kyle Hollies,L.Bryce Whitson Jr.,David Cordero,Zoltan Venczel,Chad Yoshitomi,Juan Antonio Gomez,Pedro J Temeladri,Tom Mercier,Miguel Angel Rodriguez,Jose Manuel5,Patrik Nummi2,Kenny Mitchell,Diederik Marsman,Bill Bosse,Christophe Germain,Joel Fuentes Revell,Clifford Kim,Roberto Benito,Nicolas WIART,JF Polo Marquez,David Pillon,Jean-Philippe Bousselin,Nikolaus Neumann,David Bosse,Maurizio Miconi,Gary Dangelo,Daniel Jerez Juan,Daniel Antequera,Sami-Matti Trogen,Mark Lossing,Jon Waters,Paul Russell,Ivan Soler,Oliver Jensen,Manuel Hallfarth,Christophe MEUNIER,James Andrew2,Jim Ray,Laurynas Peciukaitis,Alvaro Ferreiro Rodigues,Shad Curren,Tristan Payne,Steve Kitzmann2,Richard Berger,Jay Bart Cornelisse,Paul Cook2,Braden Ashworth,Orlando J Carbia Acevedo,David Wattecamps,Stewart Dick,Nicola Barsalona,Pablo Arriba,Tomas M Baarman,William James,David Gutiérrez Torices,Juan Maria2,Nicolas Diogo,Roman Mathis,D.B. Bowman,Daniel Penela,Peter McNair,Ivan Moral Garcia,Juan Carlos Perez Santiago,Daniel JimÃ©nez,Martin Mallette,Albert Molinero,Pedro Mendo,Marcos Ferrer,Javier García Rodríguez,Raul Villar,Corey Lewis,Filippo Da Soghe,Eric Stamp,Ben Cottle2,Sabine Hauquier,JuanRa Cercós,Florian Giacomini,Matias Canapino,Elliot Clowes,Roberto Perales,Pedro Rodrigues3,Manny Santiesteban,Joseba Elorza,Jaroslav Polma,Ronny Olsen,Xavier Schwartzmann,Diego Ferrandiz Paya,Laurent PONCET,Timothy Wade2,Paolo Cataldi,Oliver Larsen-Wright,André Martins3,Chris Hughson,Xavier Chambon,Hartmut Glöckner,Adrian Leon2,Emilio Gutierrez Suarez,Erwin Creed,Sebastien Browet,Bourgeois Loic,Rene Hauptmann2,Jason Doelle,Isidoro Campos,Christopher Hendrich,Bill Eberhardt,Joaquin Lopez Baños,Jean-Baptiste Payen,Carlos Sosa,bernat de la rosa madrid,Evan Osborne,Dylan Hale2,Daniel MARTIN2,Guillem F Antunez Saurat,Thomas Ferrière,Hafo Romero,Enrique Perez,Jorge Jorda,Ferran Pastor,Thierry CELAS2,Javier Navarro,Igor Krstinic,Ryan Sauer,Marc Llobet,Jan Willem Krab,Ranko Mijatovic,Humberto Valoura,Antonio Zaragoza,Marco Novarino,Diego Chaparro,Marcelo Hernan,Jörg Zahn,Paul Davidson2,David Salazar Baigorri,Tero Tollinen,Tony Kernan,Oliver Felske2,Thierry Rosset,Jeffrey Cook,Orlando Herrell,Pascal Mathy,Jyrypekka Lehtinen,Tougait Patrick,Chase Rivera,Mariano Pascua,Jorge BLANCO LOPEZ DEL CORRAL,Jose Divina,Mike Schittenhelm,Sergio amatriain fernandez,Paul S A Le Gallez,Carlos Rosdevall,steve Low,Eduard Pons,Radcliffe Pike,José Oñate,Jose Cobos,Ruben Alvarez Mel,Wojciech Kruk,Damian Fernandez Perez,Jordi Subirana,Raul Payan Cuevas,Yang Zhan,Ola Särnkvist,Juho Aarnio,Iván Sánchez Gallardo,Miguel Gonzalez2,Jorge Ruben Perez,Simon Jackson2,Kevin Alderman,carlos Cabanela suárez,Felix Wagner,Isabelle Bouchard,Taylor Hurst,Jake Poulin,Cristian Leonor,José Luis Oviedo de Castillejo Mo,Alejandro Rueda,Sven Deml,Kevin Toller,Jonas Kofoed2,Werner Mayerbüchler,Marco Chiodi,Gonzalo G Cuenca,Pedro Rahn,Lothar Hoffmann Marco2,Helder Loureiro,David Schwartz4,Martin Monhof,Cory Baker,Loforte Salvatore,Oscar Mauricio Lopez Nieto,Zoli Brenner,Ludovic Andre,Hans-Juergen Schreiber,Steven Cantrell,Gerardo Santillan Amuategi,Adrian Hendy,Mehdi LE GUEN,Jacky Kovo,Michal Havlík3,Yann David,Michael Pine,Alan Pereira,Chris Sewell,Alberto Rodriguez,Beaudy Cryer,Brandon S Hartwell2,George Poitou,Bob Coan,Jason Glaze,Todd Viggiano,Tobias Juda,Jordan Ashelin,Ruben Rubio,Josué Gómez,Patrik Stollhof,Rémi DAVID,Julien Schaffo,Matt Le Gallez,Aki Sandberg,Fokke Antonides,Stefan Engels,Marouane TOURHAM,Alex Robey,Jake Harvey,Rob Herridge,Matthew Hendy,Jean Jacques,James A Saunders,Michael Natera2,David Tenschert,Izaac Nicholson,Alessandro Fantinati,John Manley,Jacob Grube,Jose Borges,Asier Gonzalez Fueyo,Luis Toledo,Falk Massmann,Thibaud Van Belle,Marco Rose,Bradley Gelman,Tomas Kosina,Scott Brazier,Afonso Caetano,Ignacio Bayarri Gorbe,Kai Tormanen,Kris Butterill,Jeremiah Hornick2,Ulises V Gonzalez,Mikel Melchor2,Gavin Bramhill,Robin Austin,Genis Garcia,Hugo Haggie,Cameron Bradley,Brando Valeriano,Aitor Fernandez Valera,Jeremy Kaniewski,Fabian Hugo Scarcello,Wayne Harrison-Watt,David Piazza,Francisco Molina Rodriguez,Nicholas James Gerard,Jeffrey Anderson,David Vega,Patrick Jura,Sean P. Lyddon,Gavin Marsden,Darja Pawlowski,Gordon Gunn,Anthony Corsaro,Luiz Caruso,Christian Hoewing,Mario Stiller,Laurent Archambault,Zachary Milne,Nick Phillips,Celsino Andrade,Steven A Festa,Julien Stechele,Johan Venter,Phil Glover,Marcos Moreno,Mathieu Fortin,Jorge Bonilla,Stephen Plaske3,Renato Rmaveoito,Fran Garcia Arriaza,Steen Franta,Tim Lange,Pieter van Loon,Shawn Gray,Francisco Fernandez Garcia,Jesús Portillo Espino,Jon Keen,Borja PenÃÂ­n,Rene Zettl,Neil Bamber,David Baz,Ricky Lawson,David Polo Cabo,Daniel Bucher,Julio Cesar Hauer,Marcel Idrach2,Luan Fernandes,Ian Robson,Manuel Eicken,Antonio Baena,Omar Chammà,Tony Le Caer,Denny Wood,Guillaume Sebastien,Benjamin Arnold2,Tyler Knowlton,Luis De La Nuez,Guerric Gilbert,Michael Worrell,Jacek Madejski,julien Sales,Guy Pitra,Marc Carol,David Riobó,Ryan Himmelheber,Tim Long2,Sergio Aroca Guerrero,Peter Scheufen,Oliver Höltke,Jan Kenneth Pedersen,Khaled Megdiche,Brian Corin,Michael Schuhler,João LP Rodrigues,Daniel Del Campo Pelaez,Samu Juarez,Jarrett Herbison,Manuel Gil,Casper Nielsen,Dennis DiFrancesco,Ashley Martin,Ariel Gomez Menas,Alejandro Yanes,Matt Allen,Sergio Cerveron Domigo,Gavin Johnson,Daniel Wurth,Wojtek Koppel,Paolo Zeni,Javi Murgui Albir,Dwayne Stoddart,Lee Woodward2,Jonatan Sanzana,Maor Gueta,Shiran Kaner,Daniel Cuevas Amador,Juan Antonio Hueso,Martin Möwis,Guilherme Cravo,Joan Manero,Joshua King3,Manu Moraleda,Roberto Montero,Philipp Weber2,Julius Müller,Ian Bonta,Benno Trip,Tiago Melo,Henry Gage,Christian Raffel,Kevin Whittaker,Jose Maria Lopez,Fer Sanmartin,Jaime Adrover,Miguel Silva2,Mitchell Chapman,Rafael Andre Tobias Pinto,Nick Hörmann,Ariel Michinski,Artem Kulyaev,Wilfried Mary,Mike Hendrikx,Eloy Fernández,Scott Smith13,Kyle Manger,Alex Martens,Daniel Pinkosky,Cemil Okan Kuzey,Luis Quintela,Vlastimil Bruzek,Jörg Hartmann,Craig Harrigan,Ben Summers,Gorka Albizu,Oriol C Casas,Fernando Aguilar OcaÃ±a,David Staab,Leonardo França,Mike Smith5,Chris Murakami,Alejandro Gonzalez2,Andrew Feeney,Lex Jarecki,Jordi Guillem,Jose Gregorio Martín Linares,Francisco Verdasca,Jose Del Arco Castello,Miikka Kivipuro,Ole Schuchmann,Revellat Jordy,Dragan Mihai,Alberto Gallego2,Josh Mcdonald2,Dan Shorter,Alvaro Ortiz Linaje,Andrew Dairaghi,Fernando Costa2,Florian Kirchmann,Mauro Zapico,Sven Kabitzki,Patrick Claußnitzer,Mathias Amigoni,Craig Daley,Quique Robles,Leif Madsen,Tomislav Liber,Harry Jones,Jesus Vallin2,Rosen Bonev,Xavier Goncalves,Jake Warner,Mario Runge,Juha Peippola,Gulyás János2,Jack Freese,Iván Delgado Jareño,Jorge Bernal,Sam Needle,Eliseo Ledesma Lopez,Brandon Wilkinson,Yannick Meuleman,Richard Butler,Gerard Hendriksen2,Henk Jan Ober,Mario Gonzalez,Brendan Young,Greg Illingworth,Jonathan Boger,Ray Myers,Santi Caballero,Jose Ramon Buela,Ben M Bando,Giovanni Castiglione,Perico Zamora,Todd Trepess,James Fremont,Antonello Valentino,Paul Webster3,Pedro Delgado,Olaf Ballerstedt,Mario Lobo,Jakub Rezanina,Glen Hoffman,Kevin Pretorius,Adam Peter Kalasz,Rafael Rivera Fandos,Igor Kamennoy,Xander Hernandez,Tino Sturlic,Victor Cuesta Gonzalez,Riccardo Lamoglie,Oliver Kumpen,Gonzalo Seba Fabi,Kevin Binkley,Benjamin Maria,Shawn Baisden,Matty James,Zac Scott,Mathew Martell,Kory Tarr,Ryan Hesgard,Rees Wagen,Nathan Dowd,Adrián Pazos Vazquez2,Jordi Barrera3,ivan Lopez Lopez,Malcolm Walker,Dilieth A Ruiz,Gabor Budahazi2,Horacio Vallejos,Kuba Norbert,Robert Jagger,Joe Francis,Victor Fermin,Juan Luis Martinez,Chris Renova,Acoidan Ramos Arteaga,Karl Voss,Miguel Marcé Serra,Miguel Angel Segarra Rodrigo,Alejandro Carrizosa,Arturo Baz,Patrick Daoust,Alan Massey,Scott Wilhelmi,Ben Witt,Corey Heffron,Cody Lamb,Freddie Besems,Luke Shorte,Jason Davis3,Matt Smith-Doiron,Francois Offerman,Marc Allue,Javier Diaz4,Justin Partin,Max Louis Patric Leonhardt,Pablo Gomez2,Alejandro Juan Pellicer Bofill,Kay Stolle,Marcelino Fernandez rodriguez,Charl Hofmeyr,Pablo Rodriguez Cid,Michael Monaghan,Guido Kölsch,Matt Miles,Aaron Gerlach,Annie-Claude Bisson,Tyler Hilton,Wolfgang Janich,Carmine Sorrentìno,Marko Buhin,Pablo Vicente,Tony Ward3,Uwe Vass,Diego Leonardo,Michael Reidy2,Thibault Cazaubon7,Ricky Mack,Justin Albrecht,Jacob Tritz,Ken Wood,David Magnuson,Teo Bubicic,Moises A Caraballo,Clark Smith,Claudia Tellechea,Cedrick Hunter,Michael Guest,Aaron Mckeon,Hunter McDaniel,Michael Costello,William Owen,Stuart Pressland,Alex Kemeny,Josh Kleiva,Joel Casado Fernández,Brian Eller,Fran A.Sánchez,Estil Fields,Jarret Causby,Cesar Correa,mees van de Coterlet,Nelson Webster,Geoff Dodge,David Klebanow,Bruce Hansen,Peter Sharrocks,Jon Dunski,Antonio McCartney,Dennis Pönisch,Greg Fedoruk,Esteban Lahoz,Gareth Gonder,Jiri Muller,Roberto Grimaldi,F Javier Romero,Enrique Fernández,Manel Dupre Daroca,Craig Anderson3,Bill Swyers,Jody Baldwin2,Nichols C Hayes,Phil Kewitz,Matt Wallin,David Barile,Thomas Mammitzsch,Victor Tchakirian,Kenneth Carlson,Artie Pedersen Jr,Simon Russell2,David NARBONNE,Neil Power,Steve DeGroat,Ira Laughy,Paulo Fonseca2,Ingo Lutz,Robin Stevenson,Santiago Tirres,Bram Den Boer,Vladimir Zharko,Philip Fowler,Cesar Esteve Sanz,Michael Snow7,Daniel Garcia Diaz,Max Wright,Cale Delyea,Darren Maguire,Onur Ozben,Robert Holmes,Adri Montes,Jarkko Översti,Gergo Nagy,Jeff Donald,Rafa Soler,Sergio Bianco,Alexandre Lemaire,Alexander Toepfer,Geoff Vincent,Garrett Salmi,José R. Catalán,Kevin S. Turner,Jean-Pierre Garro,Nathan D Culley,Devin Booth,Adam Utri,Will Norman,Nicholas Randolph,Timo Görlich,Jonatan Lira,Denis Rapkiewicz,Franck Hartmann,Bert Vanbrabant,Andreas Andersson4,Bill Daily,Rob Pederson,Adam Holland,Erik Vizi,Adam State,Colin Bayes,Dean Glover,Ricard Navarro Romero,Octavio Rondoletto,Jordan Imrie,Rubén Crespo,Joe Stiefel,Peter van de Kolk,Jake Havila,Jörn Schmidt-Staade,Clyde Siazon,Christophe Dupret,Xinyan Cai,Kari-Pekka Vieltojärvi,Anthony Kearney2,Jerry Favorito,Tomoya Iitsuka,James Defeo,Nuno Aparicio2,Joseph Eaton,Jairo Via,Miguel Angel Amoedo Rey,Michele Sangalli,Florian Elsche,Cisco Schmauch,Charles Gifford,James Claeys,Juanma Perez,Benoit Toussaint,Cyril Floris,Miguel Pocho,Maik Unbehaun,Pavlos Vezirtzoglou,Lionel Gerber,Erdal Koray,Neil Odegard,Jose Ángel León,Martin Gruscher,Julian Martinez Alcala,Mohammed Islam2,Frank Lange,Christian Peinemann,Craig Napier,Tero Kokko,Gino Borsoi,Sebastian Rüther,Dario Galinke,Maria Ascension Parra Blasco,Washington Arguello,Anders Eriksson,Anton Karlsson,Florian CHAMBON,Michelle Sammet,Fraser Williamson,Ciprian Broscaru,Maxi Cerrella,Asier Juan,Pawel Pilch,Gustavo Colucci,George Polak,Mathias Schalla,Derrick Thomson,Jim Flannigan,Pablo Varela,Nando Vidal,Keith Zerafa,Stuart Haidon,Marc Denessen,Romain Lignon,Lukas Kriz,Howard Warren,Robin Leve,Neil Dawkins,André Gomes2,Sergio Vicente,Alejo González Alonso,Joseph Morgans,Jonatan Perez Rodriguez,Scott Burris2,Azril Nazli,Franz Beaulieu,Mickael FAURE,Thomas Leitgeb,Joscha Riemer,Stirbu Cristian,Scott Porath,Luis Daniel Soveral,Sean McGennisken,Thomas Doherty,Colin Scott,Michael Berg,Martin Dodig,Cristian Andrés,Brian Fowler,Daniel Rodriguez Ariza,Francisco Viles,Enrico B Bononi,Carlos Plaza,Edgar Boley,Filipe Santos5,Nathan Deher,Andres Patiño,Scott Harvie,Henri Wattecamps,Christian Keller,Eneko Peña,Cody Wyman,Petri Lankinen,Thierry Anheim,David Atkinson,Javier Perez3,Demonte Dotson,Bob Wells,Nick Holme,Felipe Fernández Laser,Marc Ventura,David Chirent,Vance Briggs,Alan Michel,Dominik Baum,Olle Andersson,Patrice Deltell,Michael Guzman,Galen Weber,Markus Acht,Marcus Fromwald,Pavel Durchev,Nikolaj Plet,sergi Verge poyato,Laszlo Z Nagy,Steven Gressent,Jonatan Caceres Monge,Gines Ruiz Nuñez,Christian Gerte,Miguel Molina Molina,Daniel Schnur,Rémi Vandaele2,Daniel Quirós,Andre Heller,Luca Stormy,Thibaud Prevot,Salvatore Gottuso,Matthew Roca2,Ivan Cunto,Diego Cabanela,Joshua Vieira,F. Cavalle Agudo,Jur Balledux,Raf Van der Linden,Jonathan Lemay,Cedric Gems,Jerzy Dudek,Luis Mas Serral2,Martin Pappa,Julien Arboux,Kiku Torres,Austin Ver Wey,Petey Karalis,Willem Prinsloo,Edward Bennett Parsons,Ross de Biasi,Brian Himmelman2,Mike Krybus,Jon Robertson,Rickey Clodfelter,Kyle Carlson,Victor Bethencourt,Pawel Kopinski,Aratz Mejuto,Matt Petkevicius,Maxence Chollet2,Jose R Lockward,Dani Bernardez Cao,Cail SanJorge2,Petr Vydra,Matthew Fox4,David Fuller3,Bertrand Fourniret,Cail SanJorge2,Bertrand Fourniret,Bill Eberhardt,Bill Eberhardt,';

$contacted = 'Bill Eberhardt,' . $current_signups . $found_in_own_races . $to_be_contacted_in_future . $personal_contacted . $indycar_road_drivers . $laguna_seca_dallara_dash_drivers . $promazda_drivers . $phoenix_dallara_dash_drivers . $me . $formula_renault_last_2 . $hosted_sessions . $spa_indycar_drivers . $skip_barber . $skip_barber2.$skip_barber3.$formula_renault2.$indycar2.$indycar3 . $promazda_2017_12 . $promazda_2017_12_2 . $indy_road_s1_2018 . $formula_renault_s1_2018_r1 . $formula_renault_s1_2018_r2_to_4 . $dallara_dash_s1_2018_w9 . $dallara_dash_s1_2018_w6 . $indy_fixed_s1_w1_w9 . $from_own_indy_fixed . $indy_fixed_iowa_tuesdays . $skippies_s1_tuesdays . $ruf_s2_2018;



//echo "\n\n\n\n\n\n\n............\nTotal number of drivers sent messages:\n".count( explode( ',', $contacted ) );die;

$contacts = '';
foreach ( array_merge(
	explode( ',', $contacts ),\
	explode( ',', $contacted )
) as $x => $driver_name ) {
	$personal_contacts[$driver_name] = true;
}



$events = array(
	'ruf-s2-wks-1-10' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	/*
	'skippies-2018-s1-tuesday-nights' => array( // might be worth inviting B-license holders too
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'fixed-indy-iowa-s1' => array( // might be worth inviting B-license holders too
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'indy-fixed-s1-w1-w9' => array( // might be worth inviting B-license holders too
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'dallara-dash-9' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'dallara-dash-6' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'formula-renault-s1-2018-r2' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'formula-renault-s1-2018-r3' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'formula-renault-s1-2018-r4' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'formula-renault-s1-2018-r1' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'indy-road-s1-2018' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'pro-mazda-s4-2017' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'indycar-s4-2017' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'indycar2' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
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

			// this removes 'Bill Eberhardt' who for some reason won't remove based on his name. appears to be an oddball character encoding problem with his name.
			if ( '215126' === $cells[5] ) {
				continue;
			}

			// Racist bit for Matt
			if (
				'Iberia' === $cells[24]
				||
				'Brazil' === $cells[24]
			) {
				continue;
			}

			// Ignore personal contacts
			if ( isset( $personal_contacts[$driver_name] ) ) {
				$drivers[$driver_name] = 'personal';
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
				$stats[$driver_name]['oval_irating'] > MIN_OVAL_IRATING
				||
				$stats[$driver_name]['road_irating'] > MIN_ROAD_IRATING
			) {
				// non problemo with iRating
			} else {
				continue;
			}

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

			}


		}

	}

}


$listed_drivers = $drivers;

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

// Strip out personal ones
foreach ( $listed_drivers as $driver_name => $track ) {

	if ( 'personal' === $track ) {
		unset( $listed_drivers[$driver_name] );
	}
}

/**
 * Finally, output names.
 */
$listed_drivers2 = 0;
if ( 'csv' === $_GET['pull_names'] ) {

	foreach ( $listed_drivers as $driver_name => $track ) {
		echo $driver_name . ',';
		$listed_drivers2++;
	}

	echo "\n\ncount: " . $listed_drivers2;


} else if ( 'details' === $_GET['pull_names'] ) {

	foreach ( $listed_drivers as $driver_name => $track ) {

		echo $driver_name . ":\n";

		if ( isset( $stats[$driver_name]['road_irating'] ) ) {
			echo "\troad iRating: " . $stats[$driver_name]['road_irating'] . "\n";
		}
		if ( isset( $stats[$driver_name]['oval_irating'] ) ) {
			echo "\toval iRating: " . $stats[$driver_name]['oval_irating'] . "\n";
		}
		if ( isset( $stats[$driver_name]['road_license'] ) ) {
			echo "\troad license: " . $stats[$driver_name]['road_license'] . "\n";
		}
		if ( isset( $stats[$driver_name]['oval_license'] ) ) {
			echo "\toval license: " . $stats[$driver_name]['oval_license'] . "\n";
		}

		$listed_drivers2++;
	}

	echo "\n\ncount: " . $listed_drivers2;

} else {

	print_r( $listed_drivers );
	echo "\n\ncount: " . count( $listed_drivers );

}


die;
