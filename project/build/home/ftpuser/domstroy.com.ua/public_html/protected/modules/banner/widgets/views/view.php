<?php

$size = [
  '1' =>[
    'w'=>'300',
    'h'=>'250',
  ],
  '2' =>[
    'w'=>'555',
    'h'=>'200',
  ],
  '3' =>[
    'w'=>'555',
    'h'=>'200',
  ],
  '4' =>[
    'w'=>'260',
    'h'=>'260',
  ],
  '5' =>[
    'w'=>'260',
    'h'=>'260',
  ],
  '6' =>[
    'w'=>'360',
    'h'=>'360',
  ],
  '7' =>[
    'w'=>'360',
    'h'=>'360',
  ],
  '8' =>[
    'w'=>'160',
    'h'=>'600',
  ],
  '9' =>[
    'w'=>'750',
    'h'=>'270',
  ],
  '10' =>[
    'w'=>'750',
    'h'=>'270',
  ],
  '11' =>[
    'w'=>'750',
    'h'=>'270',
  ],
  '12' =>[
    'w'=>'1000',
    'h'=>'90',
  ],
  '13' =>[
    'w'=>'1000',
    'h'=>'90',
  ],
  '14' =>[
    'w'=>'260',
    'h'=>'260',
  ], 
  '15' =>[
    'w'=>'260',
    'h'=>'260',
  ],
  '16' =>[
    'w'=>'260',
    'h'=>'260',
  ], 
  '17' =>[
    'w'=>'260',
    'h'=>'260',
  ],
 '18' =>[
    'w'=>'260',
    'h'=>'260',
  ], 
  '19' =>[
    'w'=>'260',
    'h'=>'260',
  ],
  '20' =>[
    'w'=>'1000',
    'h'=>'90',
  ],
  '21' =>[
    'w'=>'1000',
    'h'=>'90',
  ],
  '22' =>[
    'w'=>'555',
    'h'=>'200',
  ],
  '23' =>[
    'w'=>'555',
    'h'=>'200',
  ]
];

if ($model) {
  if ($model->position == 1) {
    echo CHtml::image(
      $model->getImageUrl(false,false,false),'"'.$model->descritpion.'"',
      array(
      'width' => $size[$model->position]['w'],
      'height' => $size[$model->position]['h'],
      'class' => 'banner-link-trigger',
      'data-target' => $model->id,
      'data-href' => $model->url,
      'style' => 'cursor:pointer;',
      'rel' => 'nofollow',
      )
    );    # code...
  }else{
    echo CHtml::image(
      $model->getImageUrl(false,false,false),'"'.$model->descritpion.'"',
      array(
      'width' => $size[$model->position]['w'],
      'height' => $size[$model->position]['h'],
      'class' => 'banner-link-trigger',
      'data-target' => $model->id,
      'data-href' => $model->url,
      'style' => 'cursor:pointer;',
      'rel' => 'nofollow',
      )
    );
  }
}

?>