clear

<#
    ������ ������������� ����� ���

#>

class EconEvaluator
{

    <#
        $evaluate_code - ��� ���������� �������
            1 - ������ ������ ����� ������ ����� ������������� ��,
            2 - 
    #>
    
    EconEvaluator()
    {

    }

    [double]evaluate([Int32]$evaluate_code, [double[]]$data)
    {

        switch ($evaluate_code)
        {

            1 {
                return ($data[0] * $data[1] * (1 + $data[2]) * (1 + $data[3]))
            }
            2 {
                return 0.00
            }

        }

        return 0.00
    }

}

class EconData
{
    
    [double[]]$input_data
    [double[]]$out_data

    EconData()
    {
        $This.input_data = @{
            "� �" = 0;
            "� ���" = 0;
            "� �" = 0;
            "� �" = 0
        }

        
        $This.out_data = @{
            "� ����" = 0;
        }
    }

    [void]InputInfo()
    {
        
    }

    [void]Input()
    {
        $This.input_data['� �']   = Read-Host "���������� ����� ����������"
        $This.input_data['� ���'] = Read-Host -Prompt "����� ����� ������ ��� �� (� �������)"
        $This.input_data['� �']   = Read-Host -Prompt "����������� �������������� ��������"
        $This.input_data['� �']   = Read-Host -Prompt "�������� �����������"

        
    }

}



$EconData = [EconData]::new()
$EconEvaluator = [EconEvaluator]::new()

$EconData.Input()

$EconData.out_data['� ����'] = $EconEvaluator.evaluate(1, @( 
    $EconData.input_data['� �'], 
    $EconData.input_data['� ���'], 
    $EconData.input_data['� �'], 
    $EconData.input_data['� �']
))

Write-Host $EconData.out_data['� ����']


