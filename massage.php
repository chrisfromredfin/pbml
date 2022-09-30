<?php
// Project data.
$raw_data = json_decode(file_get_contents('project_data.json'));

// Category data.
$category_data = json_decode(file_get_contents('category_list.json'));
foreach($category_data as $cat) {
  $categories[$cat->tid] = $cat->name;
}

// File output handle.
$output = fopen('clean_data.csv', 'w+');

// Iterate the projects.
foreach($raw_data as $project) {
  // Parse project_data, which is a serialized PHP object.
  $inner_data = unserialize($project->project_data);

  // Get the text of the category names, so we can use that.
  $this_project_cats = [];
  foreach($inner_data['taxonomy_vocabulary_3'] as $term) {
    $this_project_cats[] = $categories[$term['id']];
  }
  $this_project_cats_flattened = implode(' ', $this_project_cats);
  
  // Put in only what we want. 
  $line = [
    $inner_data['title'],
    str_replace(array('\r', '\n', ','), '', strip_tags($inner_data['body']['summary'])),
    str_replace(array('\r', '\n', ','), '', strip_tags($inner_data['body']['value'])),
    str_replace('_', ' ', $inner_data['field_project_machine_name']),
    $this_project_cats_flattened,
  ];

  fputcsv($output, $line);
}
