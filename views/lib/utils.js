/* 
 * Utilities functions
 * CAService
 */

var customClock;
var timeout = 0;

/**
 * Round UP by number of decimals
 */
function roundNumber(num, dec) {
    var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);

    return result;
}

/**
 * Parse string in a ISO8601 format
 * @param String input
 * @param String format
 * @returns {Date}
 */
function parseDate(input, format) {
  format = format || 'yyyy-mm-dd'; // default format
  var parts = input.match(/(\d+)/g), 
      i = 0, fmt = {};
  // extract date-part indexes from the format
  format.replace(/(yyyy|dd|mm)/g, function(part) { fmt[part] = i++; });

  return new Date(parts[fmt['yyyy']], parts[fmt['mm']]-1, parts[fmt['dd']]);
}

/**
 * Format date to local format
 */
function formatDateTimeString(date_string)
{
    // Avoid dash (Safari parse error)
    var date_s = date_string.substring(0,10).replace("-", "/").replace("-", "/");
    var time_s = date_string.substring(11,19);
    var fixed_date_string = date_s+' '+time_s;
    
    var date = new Date(fixed_date_string);
    
    var day = date.getDate();
    var month = date.getMonth()+1;
    var year = date.getFullYear();
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();

    if(hours<10)
        hours = '0'+hours;
    if(minutes<10)
        minutes = '0'+minutes;
    if(seconds<10)
        seconds = '0'+seconds;

    var string_date = day+"/"+month+"/"+year+" "+hours+":"+minutes+":"+seconds;

    return string_date;

//    var obj = {
//        "h": hours,
//        "m": minutes,
//        "s": seconds
//    };
//
//    return obj;
}

/**
 * Format date to local format (without time)
 */
function formatDateTimeStringNoTime(date_string, format)
{
    // Avoid dash (Safari parse error)
    var date_s = date_string.substring(0,10).replace("-", "/").replace("-", "/");
    var time_s = date_string.substring(11,19);
    var fixed_date_string = date_s+' '+time_s;
    
    var date = new Date(fixed_date_string);
    
    var day = date.getDate();
    var month = date.getMonth()+1;
    var year = date.getFullYear();

    var string_date = "";

    if(format === 'es'){
        string_date = day+"/"+month+"/"+year;
    }
    else{
        string_date = year+"/"+month+"/"+day;
    }

    return string_date;
}

/**
 * Format date to local time format (time)
 */
function formatDateTimeStringTime(date_string)
{
    // Avoid dash (Safari parse error)
    var date_s = date_string.substring(0,10).replace("-", "/").replace("-", "/");
    var time_s = date_string.substring(11,19);
    var fixed_date_string = date_s+' '+time_s;
    
    var date = new Date(fixed_date_string);
    
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();

    if(hours<10)
        hours = '0'+hours;
    if(minutes<10)
        minutes = '0'+minutes;
    if(seconds<10)
        seconds = '0'+seconds;

    var string_time = hours+":"+minutes+":"+seconds;

    return string_time;
}

/**
 * Format seconds to array
 */
function secondsToTime(secs)
{
    var hours = Math.floor(secs / (60 * 60));
    var divisor_for_minutes = secs % (60 * 60);
    var minutes = Math.floor(divisor_for_minutes / 60);
    var divisor_for_seconds = divisor_for_minutes % 60;
    var seconds = Math.ceil(divisor_for_seconds);

    if(hours<10)
        hours = '0'+hours;
    if(minutes<10)
        minutes = '0'+minutes;
    if(seconds<10)
        seconds = '0'+seconds;

    var obj = {
        "h": hours,
        "m": minutes,
        "s": seconds
    };

    return obj;
}

/**
 * Format seconds to hh:mm:00
 */
function formatTime(seconds)
{
    var time;

    // avoid dummi values
    if(seconds != 'n/a'){
        var hours = Math.floor(seconds / 3600);
        var mins = Math.floor((seconds - (hours*3600)) / 60);
        var secs = Math.floor((seconds - (hours*3600)) % 60);

        if(hours < 10)
            hours = "0"+hours;
        if(mins < 10)
            mins = "0"+mins;
        if(secs < 10)
            secs = "0"+secs;

        time = hours+":"+mins+":"+secs;
    }
    else
        time = 'n/a';

    return time;
}

function getLastDay(idMonth, idYear, cboResult) {
    
    var month=document.getElementById(idMonth).value;
    var year=document.getElementById(idYear).value;
    var dayAux = document.getElementById(cboResult).value;
    var cbo = document.getElementById(cboResult);
    var day = new Date(year, month, 0);  
    
    cbo.length = 1;
    day = day.getDate();
    var i = 1;
    for (i; i <= day; i++) {
        var option = document.createElement('option');
        option.value = i;
        option.text = i;
        cbo.add(option);
    }
    var dayFinal = i-1;
    
    if(dayAux === 0) {
        cbo.value = 0;
    }
    else if(dayAux <= dayFinal) {
        cbo.value = dayAux;
    }
    else {
        cbo.value = i-1;
    }
}

/**
 * Get time clock by custom init params
 * #progress_clock ID element needed!
 * 
 * Modified to show total hours using Array values instead of Date.
 */
customClock = (function() {

  var timeDiff;
//  var timeout;
  var timeHours = 0;
  var timeArray = new Array;

  function addZ(n) {
    return (n < 10? '0' : '') + n;
  }

  function formatTime(d) {
    return addZ(d.getHours()) + ':' +
           addZ(d.getMinutes()) + ':' +
           addZ(d.getSeconds());
  }
  
  function formatTimeFromArray(d) {
    return d[0] + ':' +
           addZ(d[1]) + ':' +
           addZ(d[2]);
  }

  return function (s) {

    var now = new Date();
    var then;

    // Set lag to just after next full second
    var lag = 1015 - now.getMilliseconds();

    // Get the time difference if first run
    if (s) {
      s = s.split(':');
      then = new Date(now);
      then.setHours(+s[0], +s[1], +s[2], 0);
      timeDiff = now - then;
      timeHours = s[0];
    }

    now = new Date(now - timeDiff);
    timeArray[0] = timeHours;
    timeArray[1] = now.getMinutes();
    timeArray[2] = now.getSeconds();

//    $('#progress_clock').val(formatTime(now)); 
    $('#progress_clock').val(formatTimeFromArray(timeArray)); 
    timeout = setTimeout(customClock, lag);
  }
}());