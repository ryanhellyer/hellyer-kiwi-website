<?php

if ( ! isset( $_GET['pull_names'] ) ) {
	return;
}


// sent
$personal_contacted = 'Robert Plumley,Ken Ehlert,Louis Richardson,Andre Moreira,Ben Wheeler,Beto Soussa,Bill Sulouff,Brandon Johnson8,Bruno Romain,Claudius Wied,Craig Crawford,Craig P Kasper,Daniel Wright4,Dave Lodl,Dennis Sather,Dominic Hoogendijk,Floyd Pate,Glen Barrett,Guilherme Carioni,James Craig4,Jamie Brinkley,Jeff Meier,Jelle Verstraeten2,Jose E. Piña,Jose Serrano,Juan Payano2,Kevin McCarthy,Kyle Schuchter,André Heidstra,Luigi Griffini,Marcello Caruso,Marcos Antonio2,Mark A Reed,Mark Voigt,Matthew Randle,Michael Johnson21,Montxo Gandia,Morten Hansen,Neil A. Jackson,Nikolay Ladushkin,Patrick Langley,Paul Rosanski,Ramon Regalado,Randy Parker,Ron Lanzafame,Steven Busuttil,Stuart Lumpkin,Szabolcs Feher,Tom Ecklein,William Norton,Zachary Luctkar,';
$indycar_road_drivers = "Marco Aurelio Brasil,Adam Plunkett,Karsten Brodowy,John Downing,Tim Holgate,Georg Naujoks,Kent Turnbull,Alexander Khursanov,Gary Powell3,A J Burton,chad Trumbla,Per-Anders Mårtensson,Bradley R Smith,Andrey Efimenko,Michele Costantini,David Adams8,Niall McBride,Andrew Kinsella,Matthew Talbert,David Hinz II,Justin Kay,Carl Johnson3,Austin Espitee,Antenor Junior2,Frederick Campbell,Andrew Stone,Serge Cantin,Karl Dronke,Sergio Morresi,Dan Lee Ensch,Rudy Avalon2,Dylan McKenna,Silviu Lazar,Bradley Walters,Andreas Eik,Jean-Marc Brunée,Anthony Cothran,Yves Bolduc,Todd Novasad,Ricardo Rossi,Wade Lear,David Warhurst,Steven Landis,Tom Rowin,Ben Ivaldi,Dimitri Djukanovic,Steves Arvisais,Christopher Hoyle,Travis Bennett,Kevin Sherker2,Tim Williams,Rados?aw Sitarz,Eric Ward2,Chris Stofer,Evan Fitzgerald,Kevin Cornelius,Yuichiro Takahara,John Lott,Zack Ditto,Josh Frye2,Francesco Sollima,Jeramie Horn,Dakota Steven Bowman,John Ahles,Joel Feytout,Andrew Jones6,Nikolay Bogatyrev,Carl Jacolette,Matt Denlinger,Tyrone Harris,Andrew Faryniarz,Tom Kotowski,Jordi Ardid Mendez,Christopher Brown4,Ryan Wilson6,Allan Moreira,Philipp Wigert,M B Dickey2,Henry Bennett,Fernando Guerrero,Rikki Gerhardt,Guillermo Domínguez,Andrea Antongini,Jussi Nieminen,John Burgess,Travis Parker,Dan Barone,Rob Powers,Santiago Nahuel Monente,Guillermo Alvarez,Enric Cabral,Harald Müller,David Henger,Kevin Holzner,JW Miller,Adam Roberson,Thomas Anton Leitgeb,John Merchant,Joachim Brückner,Luis Piñero,Henry White,Pedro Zoffoli,Lincoln Miguel,Richard Browell,Andrew Massey,Christian Steele,Rob Unglenieks,Richard Tam,Troy Eddy,Stephen Warcup,David P Pérez,Andrew Fullhart,Naruko Ishida,Bryan Carey,Doc Stout,Darren Adams2,Thomas Marmann,Richard Kaz,Neil Black,Steven Walter,Francisco Rendo,Casey Drake,Milton Thigpen,Jay Davis4,Wilson Rachid,Drew Motz,Chris Ferry2,Cesare Di Emidio2,Bernhard Jansen2,Trey Mccrickard,Jay Norris,Samuel Etter,Scott McClendon,Kleber Bottaro Moura,Jason Mayberry,Darryn Hatfield,Marcus Wohlmuth,Bob Jennings,Anthony Emery,Samuel Zinski,Pierre Bourdon,John Hess,Philip Eckert,Kaue Gomes,Mitchell Kerstetter,Mitch Walter,William Ruland,Cristian Perocarpi,Daniel Wester,David Riley2,Benton Jones,Albert Oldendorp,Adriano Fraporti,Wolf-Dietrich Hotho,Andrew Nypower,Tom Orr,Steven Clouse,Brian Beard,Rayyan Rawat,John Mignacca,José Godoy,Roger Proctor,Nate LaFluer,Harold L Stevens III,Ronnie Osmer,Brandon Trost,Arnold Estep,Tanner McCullough,Ian Layne,Joshua Witherspoon,Adam Baker5,Juan Riveros Puentes,Patrick Pierce,Blake O''Connell,Dave Jinks,Christopher Demeritt,Joe Branch2,Zach Reinke,Tim Doyle,Alesander Rodrigo,Andreas Werner,James E Davis,Brandon Clarke,Joey Bolufe,Adam Cavalla,Ken Owsley,Bruno Pagiola de Oliveira,Carlos Washington,Jan Penicka,Radek Sykora,Brandon A Taylor,Jeffrey Lacey,Joseph Wheatley,Petri Välilä,Dean Mullins,Joe Burchett,Julian Lavarias,Teemu J Rönkkö,Jason Brassfield,Cristian Otarola,Jared Wishon,Ryan Nolan,Timothy Allen2,Patrick Hingston,Matthew Montis,Carlos Neto,Christian Cabangca,Troy Thiem,Jens Roecher,Patrick Byrne,Kai Tröster,Justin Fortener,Alexander Knisely,Tyler Langenberg,Stephane Parent,Joonas Kortman,Jefferson Padovani,Kim Short,Said Gonzalez,Caeton Bomersbach,Jorge Marquinez,Olivier Dean2,Rodrigo Azevedo,Humberto Rattmann,Marco A Pereira,Wayne Sanderson,Alex Everitt,David Altman,Jeff Pritchard,Ryan Junge,Geoffrey Cervellini,Ludovic Mostacchi3,Ralf Schmitt,Nicolas Guarino,Mirco Comitardi,Anthony Gardner3,Julian Wörner2,James Robinson11,Andrew Aitken,Ronald Goodison,Tracy Drummond,Bob Martin,Jason Cange,Jeremy Hartman,Mertol Shahin,Stuart McPhaden,Peter Grey,Tim Kay,Oliver Elsen,Soeren Kolodziej,Mads B L Hansen,Jacob Yuenger,Darren Leslie,Stefan Remedy,Julian Wagg,Federico Calderon,Felipe Kaplan,Ernes Romero,Andrew S Brown,Douglas Rice,Richard da Silva,Jagoba Merino4,Jerry Foehrkolb,Todd Schneller,Joseph Plante,John Garrett,Trevor Avery,Migeon Johnny,Radoslaw Ciszkiewicz,Bill Fisher,Jake Henry,Clifford Ebben,Kian Raleigh-Howell,William Stroh,Christopher Mattas,Luis Pedreros,Jordan Lubach,J Santos,Ryan Lewis,Kyle Selig,Carlos Buritica,James Kilburn,Colin Appleton,Adam Hackman,Nico Rondet,Rodrigo Munhoz,David Corpas Benitez,Derin Pitre,Neil Andrews2,John Keefe,Andrew Cauffiel,Ryan Stein,James Woods3,Trevor Fitz,Arthur Rast,Karl Schwing,Mike A Taylor,Chris Reid,Mikey Olson,Brendan Juno,Robert Nuernberg,Timothy Roberts,Javier Garrido Vaquero,Leonardo Marques,Victor M Cano,Alexandre Martins G,Luis Sereix,Tyler Worrall,Lucas Stattel,Erick Davis,Garrett Konrath,Lucas Louly,Scott Faris,Bobby Maiden III,Brian McCraven,Andy Chadbourne,Tommy Farris,Alexandre Vinet,Jeff Yeager,Mar Vozer Felisberto,David Ross,Heamin Choi,Gage Rivait,Guillaume Pelletier,Anthony Mino,Douwe Tapper,Sam Adams,Davis Trask,Gonzalo Romero,Jacob Phillips,Michael P McVea,Justin Weaver,Brenden John Koehler,John-Paul Bonadonna,Tyler Stacy,Collin R Stark,Nicholas Soriano2,Donovan Piper,Joey Lamm,Matt M Adams,John M Roberts,Jacob Gordon,Simone Nicastro,Martijn Nagelkerken,Corey J Ott,Ricky Heinan,Marco Colasacco,Andy Crane,Sergio Lamares,Jim Flippin,Scott Beck,Paul Parashak,Brian Rainville,Yanisse Ameurlaine,Senad Kocan,Charles Crump,Blair Hamrick5,Todd Broeker,Pat Copley,Jordan Pruden,Nicholas Schmieg,Jacob Young,Andres Espinoza,Justin Parcher,Lee Hamlet,Jacob Schneider,Massimo Duse,Austin H Blair,James Pandolfe III,Ross Olson,Andy Rhodes,Christopher Hussey2,Tobias Brown,";
$laguna_seca_dallara_dash_drivers = 'Joshua S Lee,Craig Shepherd,Derek Hartford,Kevin Vogel,Eneric Andre,Alex Bosl,John Dubets,Jimmy Duncan,Albert Gisbert Falgueras,Sam Rosamond,Elliott Skeer,Robert Ulff,Oliver Patock,Robert Draper,Bruce Granheim,Mike Medley,Yusef Rayyan,John Szpyt,Nash Fry2,Barry Arends,Collin van Raam,Carl Modoff,Chris Meeth,Anver Larson,Alex Millward,Craig Evanson,Gulas Mate,Claes Poulsen,Tomasz Kordowicz,Maurice Gomillion,Brett Gardner,James Osborne4,Sepp Odoerfer,Kevin Hollinger,Craig Forsythe2,John Ellison,David Bessa Dias,Benjamin Cox,Rebelo Romain,Jeroen van Wissen,Arjan de Vreed,Gary Krichbaum2,Jonathan Heimbach,Nicholas Millard,Jake Hewlett,Alain Stoffels,Attila Papp Jr,Eddy Andersson2,Gabriel Garcia Olivares,James Strobel II,Kevin Giménez,Thibault CAZAUBON7,Derek Adams,Joris Valentin2,Robert Siegmund,Hildebrando Pinho Junior,Michael D Myers,Ben Clayden,Rob Donoghue,Elias Viejo,John Signore,Davis Rochester,Alexandre Gramaxo Gouveia,Jacob Bieser,Susan Blackledge,Robert B Eriksson,Alexandre Gravouille,Cody Siegel,Jan Spamers,Richard Sudduth,Szilard Halaszi,Shaun Barrowcliffe,Anton Kusmenko,Pierre Verne,Ryan Schartau,Florian Bayle,Vincent Hamet,Guillaume Dupont2,Imre Lukacs,Romain Pelissier,Xisco Fernández,Loris Amadio,Benjamín Carreiras,Andre Monteiro2,Robert Long2,Braden Graham,Duncan Watt,Paul J Ulliott,Tom Berendsen,David Strickland,Antonio Bermúdez,Scott Nicholson Jr,Adolf Egli,Sergi Maturana,Manuel Bañobre,Quinten Vermeulen,Javier Perez M.,Frederick Zufelt,Brendan Lichtenberg,Arjan de Vreede,Sebahattin Atalar,Raphael Lauber,Christian Rose2,Paul Huber,Simon Etheridge,Lawrence Phipps2,Javier Isiegas,Tye Macleod,Lee Jenner,Fran Lucas,Lukas Winter,Ryan Bird,Joel Stampfli,Zachary Sober,Robert McNeal,Brad Teske,';
$promazda_drivers = 'Joshua S Lee,Craig Shepherd,Derek Hartford,Kevin Vogel,Eneric Andre,Alex Bosl,John Dubets,Jimmy Duncan,Albert Gisbert Falgueras,Sam Rosamond,Elliott Skeer,Robert Ulff,Oliver Patock,Robert Draper,Bruce Granheim,Mike Medley,Yusef Rayyan,John Szpyt,Nash Fry2,Barry Arends,Collin van Raam,Carl Modoff,Chris Meeth,Anver Larson,Alex Millward,Craig Evanson,Gulas Mate,Claes Poulsen,Tomasz Kordowicz,Maurice Gomillion,Brett Gardner,James Osborne4,Sepp Odoerfer,Kevin Hollinger,Craig Forsythe2,John Ellison,David Bessa Dias,Benjamin Cox,Rebelo Romain,Jeroen van Wissen,Arjan de Vreed,Gary Krichbaum2,Jonathan Heimbach,Nicholas Millard,Jake Hewlett,Alain Stoffels,Attila Papp Jr,Eddy Andersson2,Gabriel Garcia Olivares,James Strobel II,Kevin Giménez,Thibault CAZAUBON7,Derek Adams,Joris Valentin2,Robert Siegmund,Hildebrando Pinho Junior,Michael D Myers,Ben Clayden,Rob Donoghue,Elias Viejo,John Signore,Davis Rochester,Alexandre Gramaxo Gouveia,Jacob Bieser,Susan Blackledge,Robert B Eriksson,Alexandre Gravouille,Cody Siegel,Jan Spamers,Richard Sudduth,Szilard Halaszi,Shaun Barrowcliffe,Anton Kusmenko,Pierre Verne,Ryan Schartau,Florian Bayle,Vincent Hamet,Guillaume Dupont2,Imre Lukacs,Romain Pelissier,Xisco Fernández,Loris Amadio,Benjamín Carreiras,Andre Monteiro2,Robert Long2,Braden Graham,Duncan Watt,Paul J Ulliott,Tom Berendsen,David Strickland,Antonio Bermúdez,Scott Nicholson Jr,Adolf Egli,Sergi Maturana,Manuel Bañobre,Quinten Vermeulen,Javier Perez M.,Frederick Zufelt,Brendan Lichtenberg,Arjan de Vreede,Sebahattin Atalar,Raphael Lauber,Christian Rose2,Paul Huber,Simon Etheridge,Lawrence Phipps2,Javier Isiegas,Tye Macleod,Lee Jenner,Fran Lucas,Lukas Winter,Ryan Bird,Joel Stampfli,Zachary Sober,Robert McNeal,Brad Teske,';
$me = 'Ryan Hellyer,';
$phoenix_dallara_dash_drivers = 'William Swenson,Donnie Sanders,Liam Quinn,Steven Freiburghaus,Kurtis Mathewson,Joao Valverde,Bryant Ward,Tarmo Leola,Michael Kildevaeld,Garrett Cook,Hartmut Wagner,Alejandro Leiro,Frank Bieser,David Keys,Balazs Floszmann2,Maxime Potar,Aymerick Vienne,Jason Lowe4,Kevin Shannon,Henry Eric,Patrice Lebrun,Rémi Picot,Timothy Scanlan,Philippe Tortue,Antoine Gobron,Neil Thompson,Pascal Bidegare,Mathieu NEU,Kirk Smith,Laszlo Miskolczi,Sam Cook4,Jim Gibbs,Dale Robertson,Kelly Thomas,Adolfo Macher,Denis Nestor Kieling Kieling,Maik Lara Guerra,Richard Grimley,Herve Lanoy,Jesus Fraile Hernandez,Justin Adakonis,Matthew Carter7,Art Seeger,Martin Vaughan,Rob Collister,Jake Johannsen,Tyler Rahman,Jan Schumacher2,Esa Hietanen,Vahe Der Gharapetian,Jake Conway,Fernand Frankignoul,Daniel Förster,Joel Taylor,Marti Olle,Patrick Weick,Thierry Schmitt2,Rodney Bushey,Michel Rugenbrink,Zack Tusing,Trever Halverson,Peter Labar2,Steven Roberts4,Dirk Rommeswinkel,Daniel Redlich,Brian Spotts,Isaac Jaen,GÃ©rard AMBIBARD,Pablo Perez Companc,Robert Queen,Dewey Perry,Joshua Halvey,Charles Hinkle,Helio Santos,Austin Collings,Adam Smith5,Austin Eder,Joshua Baird,Harry Floyd,Joseph Scatchell,Dardo Nosti,Mario Alvarez,Michael Erian,Colin Earl,Kenneth Webb,Dave Dawson,Paulius Dunauskas,Trevin Dula,';

$formula_renault_last_2 = 'José Cantharino,Michael H Burton,Tae Kim,Mauricio Moreno2,Rob McGinnis,Chester Thompson,Pavel Philippov,Tony Vasseur2,Phil Letchford,Michelle Smeers,Jesus Manuel,Greg Garis,Jack Freke,Daniel Nyman,Dan Mocanu,Paul Nopper,Walter Bornack Jr,Charlie Rage,Augusto Henriques,Marco Van Wisse2,Saúl Quintino Santana,Vittorio Saltalamacchia,Rene Maurer,David Rojo Lopez,Cameron Dance,Kris Roberts,David VELASQUEZ,Viktor Shubenkov,Alexander Stock,Jerome Haag,Matt Bobertz,Fabio Aoki,Admir Nevesinjac,Stefano Calascibetta,Christopher Gray3,Marc Oros,Don Bowden,Andy R Moore,Roger Prikken,Naiche Barcelos,Daniel Berry,Fredrik Kvarme,Sergi Viñolas Font,Paul R Lewis,Marcos Silveira2,Curtis Martin2,Adam Leff,David Hoffmann,adam Saunders2,Ranford S Brown,Matt Cox,Salva quadradas,Terry Taylor,Sergi Cardó Catalan,Chris Homann,Reid Harker2,Andy Bonar,Tony Matthews,Artur Gozdzik,Manuel Heuer,Joonas O Vastamäki,David Waldoch,Lubomir Moric,Egoitz Elorz,Daniel Barrero,Scott Clarke,Gerald Zindler,Sandro Biccari,Rogier Visser2,Kouji Itoh,Takuya Sekimoto,Antonio Ortiz Poveda,Alexis Mattaruco,Francesco Tonin,Philippe Silva,Thomas Glad,Scott McClintock,Thiago Spencer,Damon Martinez,Jeremy Skinner,Tim Delisle,Jeroen De Quartel,Chris Burgess,Jerome Lapassouze,Jonas Toigo de Souza,Andrea Brachetti,Rogerio Schiavon,Marcio Marodin,Kristof De Busser,Adam Frazier,Antony Perryman,Angus Waddell,Tim McIver,Nikolay Andreev,Jorge Rolandi,Jorge D Romero,Dave Martin,Kevin Fennell,Sebastien Kendzierski,Stuart John,Thomas Scheuring,Lopez Alexandre,Angel Garrido Orozco,Bruno Domiter,Dimitri Coulon,Alejandro Blanco,Carles Bayot,Hannes Wernig,Bruce Keys,Marc Leyes,Ronnie Olsen,Caroline Viscaal,Janneau Rompelberg,Dennis Gerressen,David Buitelaar,Jakub Jezierski,Sakiran Partowidjojo,Domenic Guras,Daniel Gazquez,Christian C Meyer,Raul Alfonso Valero Llordes,Fabrizio Donoso,Peter Berryman,Frank van Brandwijk,Phil Reid,Claudio Candio,Martin Donati,Charles Stark,Anibal Colapinto,Teemu Kotila,Adam Tierney,David Porcelli,Patrick Wartenberg,Carlos Cabral,Marcos Marcelo,Claudio Costarelli,Alex Düttmann,Bruce Poole,SeongWon Jeong,Ossi Varjoranta,Steven Campbell,Mario Ledesma,Ken Daly2,Joseph Nelson2,Paul Trgovac,Daniel Cunha Prado,Miroslav Ju?í?ek,Aaron Turner,Pedro Henrique Moisés,Frederic Guillaume,Edward A Samborski,Derwyn Costinak,Jose Alberto Hidalgo Nicolas,Brent Mills,Cuba Gonzalez,Alberto Pérez,Evan RT Imray,Giancarlo Bartolini,Jack Hobbs,fabrice BAZIN,Johan Hellemans,Robert Dilbeck,Davide Giovanni Solbiati,Thomas Mrazik,Alejandro Alvarez3,Sam Satchwell,Patrick Luzius,Oscar Quiroga,Richard Schouten,Ralph Thomson,Alex Stumpe,Anto Mn,Erkka Lindström,Renaud Soudiere,David Jarvis,Victor Martins,Christian Schultz,David Sanchez5,Daniel Clarke,Jose Pleguezuelos Langa,Stéphane LEMAIRE,Edmundas Azlauskas,Hiroshi Sakai3,Marco Cesana,Jari Viinamäki,Mickel Francis,Josh Slade,David Thompson12,Kurt Bagnell,Dylan Prostler,Sergio Pasian,Jose Telmo,Elliot Leach,Scott Lefeber,Alan Hix,Marc Martinez Forner,Jeremy Lapainis,';



// ********** NOT SENT YET ***********
$formula_renault_last_2_batch_one = 'Ian Dimmock,Daniel Bridgman,Mark Cossins,Manel Segret,Damien Courtaillier,Patrick Søby,Patrick Fleischer,Tanguy Pedrazzoli2,Daniel Fletcher,Julia Pros Albert,Alistair Hay,Harrison Finch,Manfred Bachinger,Michael Jeanes,Robin De Becker,Marcel Boersma,Jannik D Strelow,Matt Malone,Brandon Crowe,Koichi Wakiyama,Shogo Yamagata,Jonathan Croteau-Dicaire,Thomas Ingram,Marc Estruch,Matthew Pepperell,Estruch Christophe,Don Manuel,Kurt Buyst3,Jack Sadler,Ivan Mula Vivero,Gareth Hickling,DongJae Shin,R Schouten2,Christophe Brengard,Rian Moore,Chris Crawford4,Luciano C Pereira,Nikolaj Plet,Jorge Jorda,Miguel Angel Muñoz Juarez,Jorge Cabrita,Guillaume Sebastien,Mamoud Tetteh,Wibo Pot,Igor Kalinin,Paul Scutt,Carlos Fernandez Espinosa2,Antonio Iglesias Osuna,Hugo Queniat,Sergi Martinez,Miguel Antolin,Gerard Cabrera,Anthony Lepagnot,Sebastian Peter Schaar,Mickael Patient,Jan Janás,Igor Ghisi,Charles Germain3,Jorge Reinoso,Javier M. Alonso,Sebastien Brossard,Justin Bhadjan,Wouter Remmerswaal,Paolo Varolo,Ben Hutchinson,Stephane Boyer,Martin Brouwers,Gabi Moreno Cordoba,Mark Bartholomew,Jorge Fernandez,Tomasz Hamryszak,Ricardo Pallavidino,Achille Cinquegrana,Chris Knight,Yohann Harth,Roel Houben,Sébastien PETIT,Mark Jarvis,Martin Cruz,John Siemer,Gisler Sascha,Jaime Duque C,Lance Simon,Andres Guindi,Raymond Darwent,Ayrton Shelly,Mitchell J Kunst,Pelayo Lastra Garcia,Roberto Stefano Kovac,Jens Falk,David H. Santana,Alexis Meireloso,Stefan Mommen,Vincent Revel,Carlos Castilla,Rob Boer,Diego Jiménez Hormilla,Santtu MÃ¤kinen,Thomas Zieger,I.M Keeman,Ivan Alvarez,Vincenzo Marchiafava,Markus Kuttelwascher,Jose Portilla,Joao Dario,Baden Swain,Matt Miller,Charles Couture,Clive Norton,Luc Couture,Linden Swaby,Greg McEneaney,Stephen R Smith,Lyssimacos Maraslis,Jose Cortes Sanchez,Carlo Morman';
$formula_renault_last_2_batch_two = 'Luis Gonçalves,Ainsley Martin,Steve Rock,Christian Målmark,Andrew Berry2,Jose Antonio Camara Paredes,Joao Paulo Ferreira,Jose Mariscal,Danny Navas,Daniel PILUSO,Luke Barton,Sandra Castrogiovanni,Marco Oliveira2,Carlos Newmar,Gerry Moersdorf,Montserrat Cervera,Jeremy Shelton,Andy Corkhill,Mauricio Tjon-Sien-Fat,Marcelo Bradaschia,Luc Fontaine,Ransom Cubitt,Kirk Schneider,Jeff Huh,Phillipp Urquhart,Kasper Thrane,Gabriel Borghi,Scott B Layton,Alec Hudson,Christopher Sykes,Friedrich Ladwig,Julien François,Charlie Summers,Mike Cannon,Borja Bueno,Duarte Pires,André Machado Domingues,Aitor Cantó Prieto,Felipe Mallada,Jonatan Lira Vilches,Dennis Zegers,Naeem Ali,José Luis Duran Gonzalez,Javier Navarro,Arne Stops,Francisco González,Timo Pruijn,Simon Christmann,Aitor Garcia,Karl-Johan Hagelin,Anthony Catt,Joseph Tavora,Jarrod Cook,Danny Zwoferink,Juan Maldonado,Patrick Gallo,Eric W. Patterson,Nate Ulrich,Doug Chappell,Joshua Stuart,Kenny Tinnion,Josh Radford,Fernando C Rodriguez,Joakim Jansson2,Kevin Whittaker,James Merriman,Marinho Silva,Ricardo Ruy,Francisco Canovas,Alex Thornton,Jokin Ortega Zugazaga,Elijah Bautista,Stephen Day,Helmut Weber3,Balta Fernandez,Michael Schlegelmilch,Shaun Abbate,Nathaniel Simpson,Chris Lazarevic,Alex Baciu,Jamie McKnight,Pedro Miguel Santos,William Moody2,Michael Bergh,Nuno Santos,Alaric van den Bos,Daniel Medina,Edy Carlos,Lynn Kennedy,Tim Bailey3,Mike Powers,Tibor Wittmann,James Bulmer4,Joseph Curcio,Eude Cyrille,JBen Cote,Edward Schofield,Sasa Varga,Carlos Ventura,Mustafa N. Erden,Thierry Daveine,Timo Kraske2,Marco Lodi Rizzini,Christopher Block,Hans Hasperhoven,John Mcconville,Josh Thompson,Simone Maria Marcenò,Jason Baird,Antonio Amaro,Scott Newton2,Leandro Bignardi,Matt Carr,Wyatt Nelmes,Martin Brandon,Jose Gregorio Martín Linares,Jean Marc Rey,Toni Andrade,Rubens Barrichello,Ashley Howell,Sergio Jimenez,Tamas szene,Zakaria Riat,Brandon Ziegler,Alessandro A Sais,Philippe Schyns,Maarten van Loozenoord,Olli Kwoka,Jan Schwitter,Mario Gil,Paulo Blanco,Lionel Oberto,Juan Carlos Botia Lorca,Christoph Aymon,Dan Kilbank,Zafer Derelioglu,Enrique Navarro Izquierdo,Daniel Sibbons,Hans Hennekes,Santiago Carducci,Enrique Tramontin,Liam Gordon,Benedikt Huber,Michael Duforest,Guillermo Dieste Ponzán,Axel Stasse,Jared de Kruijff,Karolis Jovaisa2,Laurynas Vaitiekunas,Tom Depke,Gonzalo Camara,David Sanchez Lopez,Xavier Empereur,Julien jourdan,Alexander Koffard,Dustin Williams2,German Jimenez Ramirez,Jeroen Pol,Keisuke Shouji,Jenno Buelders,C Bibo,Donald Sadusky,Roberto Alvarez,Henri Brieskorn,Jouni Silvasti,Richard ARNAUD,Knut Martinsen,Bob van Katwijk,Luiz Felipe,Eric Kilen,Marcos Aroztegui,Kevin Cochran,Mariano Martin Garcia,Thiago Canola,Anthony Burroughs,Brian Chatfield,Mark W Turner,Landan Nash,Joel Raymond,Dan McGuirk,Luis Moedinger,Aitor Martinez Bertomeu,Trym Selvag,Harry Cowan,Sean Gaffney,Marco Barbanera,Daniel Casas,Kyle Vallejos,Derek Carmenate,Grant Henderson2,Tom Andre Munkhaug,Antonio Trueba Buenaga,Jayden Thurlow,Miguel Cela,Jens Paul,Neil Bontems,Dave Sanchez,Samuel Martínez,Alessandro Tolfo,Guillermo Leon de la Barra,Fernando Dasilva,Gary Weaver,Miguel Silva2,Brandon DeGuire,Jason Trifonov,Mitchell Mullens,Rafael Acquesta,Paulo Nogaroto,JP Foster,Barry Merinsky,Michael Morris6,Antonio Urbano Checa,Kyle Bucklew,Sam Dunstall,Rene Penquitt,Miguel Aguilar,Isaac Silva Gonzalez,Miguel Monroy,Dustin Bouchard,Jon Ford,John Castillo,Miguel Invernon de Julian,Blasius Giers,Mickael Moreau,Vincent Genereux-de Guise,Lluc I.Trulolls,?ukasz Walczak,Andre Duennbier,Anthony Obrohta,Lars Ole Wandel,Chris Ritter,Boban Matic,Michael Partington,Andrew Jones2,Paul Carter3,Marie-Helene Bredeaux,Rémi courtey,William Marshall,Bruno Oliveira5,Paul Denton,Juuso Niinivaara,Alberto Gomez C,Andrés Mora,Daniel C.Lentijo,Alex Ellis,Reece Ushijima,Martin Lippincott2,Brian Duray,Paul J Brown,Michael Geringer,Átila Peretto dos Reis,Jorge Destro,Martin Pereira,Luciano Fernandes,Alexander Galahov,Kazuki M Tanaka,Christopher DeHay,James Beardsley,Josh Conquer,Aaron Hernandez Ponce,Alberto casas2,John Gibson5,Adam Illman,Mariusz Gromiec,Aitor Mediavilla,Felix Pollan Tome2,Bekir OZAKIN,Stefan Ritter,Joonas Haapiainen,Axel Straub,Alistair Glen2,Matthieu Chauvière,Adam Alicki,Daniel Abal Freire,Jesus Martin Sanchez,Aart Kraaijenhof,Peter Parnell,Andre Heller,Jonathan Hambrick,Igor López,Phoenix Tucker,Olivier de Wilde,Guus Franken,Silvio Hirt,Davy Schaepdryver,Eduard Clavero,Amal Youness,Stephen Baxter2,Yoshiki Nagashima3,Benjamin Brenot,Victor Pijpers,Jos Honig,Kuuya Takabayashi3,Matt Harvey2,Christoph John,Steven Thompson2,Nick Percat,James Mikrut,Connor Clifford,Milan Gavric,';




//ADD FORMULA RENAULT AND SKIP BARBER!




/********************

s			MASSIVE BUG --- THIS SYSTEM IS SENDING DOUBLE AND TRIPLE MESSAGES

***********************/


$contacted = $personal_contacted . $indycar_road_drivers . $laguna_seca_dallara_dash_drivers . $promazda_drivers . $phoenix_dallara_dash_drivers . $me . $formula_renault_last_2;

$contacts = '';
foreach ( array_merge(
	explode( ',', $contacts ),
	explode( ',', $contacted )
) as $x => $driver_name ) {
//	$personal_contacts[$driver_name] = true;
}



$events = array(
/*
	*/
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
);

foreach ( $events as $event => $vars ) {

	$incident_ratio_1 = $vars['incident_ratio_1'];
	$incident_ratio_2 = $vars['incident_ratio_2'];
	$incident_ratio_3 = $vars['incident_ratio_3'];
	$time_1 = $vars['time_1'];
	$time_2 = $vars['time_2'];
	$time_3 = $vars['time_3'];
	$directory = '/home/ryan/Downloads/dash-results/' . $event . '/';


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

			// Warn about unfound drivers
			if ( ( ! isset( $stats[$driver_name]['road_license'] ) && ! isset( $stats[$driver_name]['oval_license'] ) ) ) {

				// No license so do strict time check
				if ( $time > $time_2 || 0 == $time ) {
					// too slow or no time set, so bail out
					continue;
				} else {

					if ( $incident_ratio < $incident_ratio_2 ) {

						// Not licensed, but fast enough and little incidents, so lets allow them anyway
						$drivers[$driver_name] = $event;

					}

					continue;

				}

			}

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
				) {
					$drivers[$driver_name] = $event;
					continue;
				}

			}

			if ( isset( $stats[$driver_name]['road_license'] ) ) {

				// Allow A B C drivers
				if (
					'A' === $stats[$driver_name]['road_license']
					||
					'B' === $stats[$driver_name]['road_license']
					||
					'C' === $stats[$driver_name]['road_license']
				) {
					$drivers[$driver_name] = $event;
					continue;
				}

				// Only allow safe D drivers
				if (
					'D' === $stats[$driver_name]['road_license']
					&&
					$incident_ratio < $incident_ratio_3
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

	if (
		'formula-renault-last-2' !== $track
	) {
//		echo $driver_name . ': ' . $track . "\n";
		unset( $listed_drivers[$driver_name] );
	}

}


if ( 'csv' === $_GET['pull_names'] ) {

	foreach ( $listed_drivers as $driver_name => $track ) {
		echo $driver_name . ',';
	}

} else {
	print_r( $listed_drivers );
}

echo "\n\ncount: " . count( $listed_drivers );;

die;
