<?php

namespace Drupal\quizz_migrate\Branch4x\AnswerDetails;

class ClozeDetails extends BaseDetails {

  protected $source_table_name = 'quiz_cloze_user_answers';
  protected $source_columns = array('answer_id', 'result_id', 'question_nid', 'question_vid', 'score', 'answer');
  protected $dest_table_name = 'quiz_cloze_answer';
  protected $column_mapping = array(
      'answer_id'    => 'answer_id',
      'result_id'    => 'result_id',
      'question_nid' => 'question_nid',
      'question_vid' => 'question_vid',
      'score'        => 'score',
      'answer'       => 'answer',
  );

}
