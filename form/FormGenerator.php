<?php 

class FormGenerator
{
    private $formData;
    private $currentUserRole;

    public function __construct($jsonFilePath, $currentUserRole)
    {
        $this->formData = json_decode(file_get_contents($jsonFilePath), true);
        $this->currentUserRole = $currentUserRole;
    }

    public function generateForm()
    {
        $html = '<form id="contactForm" class="form-auth m-0" action="../contact/includes/contact.inc.php" method="post">';
        $html .= insert_csrf_token();
        $html .= '<h6 class="h3 mb-3 font-weight-normal text-muted">Contact Us</h6>';
        
        foreach ($this->formData['fields'] as $field) {
            if ($this->hasPermission($field)) {
                $html .= $this->generateFormField($field);
            }
        }
        $html .= '</form>';
        return $html;
    }

    private function hasPermission($field)
    {
        $readPermission = $field['field_read_permission'];
        $writePermission = $field['field_write_permission'];

        // Check if the current user's role has the required permissions
        return ($this->currentUserRole === $readPermission || $this->currentUserRole === $writePermission);
    }

    private function generateFormField($field)
    {
        $fieldType = strtolower($field['field_type']);
        $fieldName = $field['field_name'];
        $fieldId = $field['field_id'];
        $defaultValue = $field['default_value'];
        $options = isset($field['options']) ? $field['options'] : [];

        $html = '';

        switch ($fieldType) {
            case 'text':
                $html .= '<div class="form-group">';
                $html .= '<label class="" for="' . $fieldId . '">' . $fieldName . '</label>';
                $html .= '<input class="form-control" type="text" id="' . $fieldId . '" name="' . $fieldId . '" value="' . $defaultValue . '">';
                $html .= '</div>';
                break;
            case 'textarea':
                $html .= '<div class="form-group">';
                $html .= '<label class="" for="' . $fieldId . '">' . $fieldName . '</label>';
                $html .= '<textarea class="form-control" id="' . $fieldId . '" name="' . $fieldId . '">' . $defaultValue . '</textarea>';
                $html .= '</div>';
                break;
            case 'password':
                $html .= '<div class="form-group">';
                $html .= '<label class="" for="' . $fieldId . '">' . $fieldName . '</label>';
                $html .= '<input class="form-control" type="password" id="' . $fieldId . '" name="' . $fieldId . '" value="' . $defaultValue . '">';
                $html .= '</div>';
                break;
            case 'select':
                $html .= '<div class="form-group">';
                $html .= '<label class="" for="' . $fieldId . '">' . $fieldName . '</label>';
                $html .= '<select class="form-control" id="' . $fieldId . '" name="' . $fieldId . '">';
                foreach ($options as $option) {
                    $html .= '<option value="' . $option . '">' . $option . '</option>';
                }
                $html .= '</select>';
                $html .= '</div>';
                break;
            case 'radio':
                $html .= '<div class="form-group">';
                $html .= '<p>' . $fieldName . '</p>';
                foreach ($options as $option) {

                    $html .= '<label class="radio-inline mr-2">
                    <input class="mr-2" type="radio" name="' . $fieldId . '" checked>' . $option . '
                  </label>';

                }
                $html .= '</div>';
                break;
            case 'checkbox':
                $html .= '<div class="form-group">';
                $html .= '<input class="form-control" type="checkbox" id="' . $fieldId . '" name="' . $fieldId . '" value="' . $defaultValue . '">';
                $html .= '<label class="" for="' . $fieldId . '">' . $fieldName . '</label>';
                $html .= '</div>';
                break;
            case 'submit':
                $html .= '<div class="text-center mx-5 px-5">
                            <input class="btn btn-lg btn-primary btn-block" type="submit" name="' . $fieldId . '" id="' . $fieldId . '" value="' . $fieldName . '">
                        </div>';
                break;
        }

        return $html;
    }
}
