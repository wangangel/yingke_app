 $(function () {
        $("#txtBeginDate").calendar({
            controlId: "divDate",                                 // ���������ڿؼ�ID��Ĭ��: $(this).attr("id") + "Calendar"
            speed: 200,                                           // ����Ԥ���ٶ�֮һ���ַ���("slow", "normal", or "fast")���ʾ����ʱ���ĺ�����ֵ(�磺1000),Ĭ�ϣ�200
            complement: true,                                     // �Ƿ���ʾ���ڻ���հ״���ǰ���µĲ���,Ĭ�ϣ�true
            readonly: true,                                       // Ŀ������Ƿ���Ϊֻ����Ĭ�ϣ�true
            upperLimit: new Date(),                               // �������ޣ�Ĭ�ϣ�NaN(������)
            lowerLimit: new Date("2011/01/01"),                   // �������ޣ�Ĭ�ϣ�NaN(������)
            callback: function () {                               // ���ѡ�����ں�Ļص�����
                alert("��ѡ��������ǣ�" + $("#txtBeginDate").val());
            }
        });
        $("#txtEndDate").calendar();
    });