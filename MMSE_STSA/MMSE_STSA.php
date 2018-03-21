<?php

function MMSE_STSA_init() {
  $init = array(
    "java" => array(
      "type" => "cmd",
      "required" => "required",
      "missing text" => "MMSE_STSA requires Java.",
      "version flag" => "-version"
    ),
    "javac" => array(
      "type" => "cmd",
      "required" => "required",
      "missing text" => "MMSE_STSA requires Java compiler.",
      "version flag" => "-version"
    )
  );
  return($init);
}

function MMSE_STSA_prepare() {
  exec("cd modules/traits-MMSE_STSA/MMSE_STSA/AudioProcessor; javac DenoisingExample.java; cd ../../../..;", $output, $return_value);
  if ($return_value != 0) {
    core_log("fatal", "MMSE_STSA", "Could not compile DenoisingExample.java: ".serialize($output));
  }
  return(array());
}

function MMSE_STSA_transcode($data) {
  global $system;
  $return = array();
  if (!in_array($data["id"].".MMSE_STSA.wav", $system["analyses"]["wav"])) {
    core_log("info", "MMSE_STSA", "File ".$data["id"]." needs MMSE_STSA version.");
    $file = core_download("wav/".$data["id"].".wav");
    if ($file = NULL) {
      core_log("warning", "MMSE_STSA", "File could not be downloaded.");
      return($return);
    }
    $return[$data["id"]] = array(
      "file name" => $data["id"].".wav",
      "local path" => "scratch/wav/",
      "save path" => NULL
    );
    exec("java -Xmx20148m -classpath modules/traits-MMSE_STSA/MMSE_STSA/AudioProcessor DenoisingExample scratch/wav/".$data["id"].".wav", $output, $return_value);
    exec("mv scratch/wav/".$data["id"]."_enhanced.wav scratch/wav/".$data["id"].".MMSE_STSA.wav");
    if ($return_value == 0) {
      $return[$data["id"]."1k_MMSE"] = array(
        "file name" => $data["id"].".MMSE_STSA.wav",
        "local path" => "scratch/wav/",
        "save path" => "wav/"
      );
    } else {
      core_log("warning", "bioacoustica", "Could not download file for BioAcosutica recording ".$data["id"].".");
    }
  }
  
  if (!in_array($data["id"].".1kHz-highpass.MMSE_STSA.wav", $system["analyses"]["wav"])) {
    core_log("info", "MMSE_STSA", "File ".$data["id"]." needs MMSE_STSA version.");
    $file = core_download("wav/".$data["id"].".1kHz-highpass.wav");
    if ($file = NULL) {
      core_log("warning", "MMSE_STSA", "File could not be downloaded.");
      return($return);
    }
    $return[$data["id"]] = array(
      "file name" => $data["id"].".1kHz-highpass.wav",
      "local path" => "scratch/wav/",
      "save path" => NULL
    );
    exec("java -Xmx20148m -classpath modules/traits-MMSE_STSA/MMSE_STSA/AudioProcessor DenoisingExample scratch/wav/".$data["id"].".1kHz-highpass.wav", $output, $return_value);
    exec("mv scratch/wav/".$data["id"].".1kHz-highpass_enhanced.wav scratch/wav/".$data["id"].".1kHz-highpass.MMSE_STSA.wav");
    if ($return_value == 0) {
      $return[$data["id"]."1k_MMSE"] = array(
        "file name" => $data["id"].".1kHz-highpass.MMSE_STSA.wav",
        "local path" => "scratch/wav/",
        "save path" => "wav/"
      );
    } else {
      core_log("warning", "bioacoustica", "Could not download file for BioAcosutica recording ".$data["id"].".");
    }
  }
  return($return);
}
