/**
 * Setting globals for use across all scripts.
 * These are set as globals to avoid rebuilding them continuously.
 */
var audioFileDir     = window.location.origin + "/audio/";
var audioFile;
var mainHeader      = document.getElementById( "header" );
var mainFooter      = document.getElementById( "footer" );
var volumeWrapper   = document.getElementById( "volume-wrapper" );

// Audio player
var audioPlayer      = document.getElementById( "audio-player" );
var volumeValue      = document.getElementById("volume-value");
var play             = document.getElementById("play");
var durationTime     = document.getElementById( "duration-time" );
var currentTime      = document.getElementById( "current-time" );
var timeControl      = document.getElementById( "time-stamp" );
var mute             = document.getElementById( "mute" );
var volumeControl    = document.getElementById( "volume-control");
var timeElapsedLine  = document.getElementById( "time-elapsed-line" );
var repeatButton     = document.getElementById( "repeat-button" );
var shuffleButton    = document.getElementById( "shuffle-button" );
var trackDescription = document.getElementById( "track-description" );
var thumbsDown       = document.getElementById( "thumbs-down-value" );
var thumbsUp         = document.getElementById( "thumbs-up-value" );

// Menus
var hamburgerMenu    = document.getElementById( "hamburger-menu" );
var headerNav        = document.getElementById( "header-nav" );

// Main content
var main             = document.getElementById( "main" );
var title            = document.getElementById( "title" );
var content          = document.getElementById( "content" );
var canvas           = document.getElementById( "canvas" );
var comments         = document.getElementById( "comments" );
