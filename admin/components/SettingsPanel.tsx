import React, { useState } from 'react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Switch } from "@/components/ui/switch";
import { Button } from "@/components/ui/button";
import { Plus } from "lucide-react";
import { useToast } from "@/components/ui/use-toast";

const SettingsPanel = () => {
  const { toast } = useToast();
  const [settings, setSettings] = useState({
    showName: true,
    showPhone: true,
    showEmail: true,
    showAddress: true,
    showCity: true
  });

  const handleSaveSettings = () => {
    // Here we'll add the actual save functionality later
    toast({
      title: "تم حفظ الإعدادات",
      description: "تم حفظ التغييرات بنجاح",
    });
  };

  const handleAddField = () => {
    // Here we'll add the custom field functionality later
    toast({
      title: "إضافة حقل جديد",
      description: "سيتم إضافة حقل جديد قريباً",
    });
  };

  return (
    <div className="p-6 bg-white rounded-lg shadow-sm max-w-3xl mx-auto">
      <h2 className="text-2xl font-bold mb-6 text-right">إعدادات فورم الطلب</h2>
      
      <Tabs defaultValue="fields" dir="rtl" className="w-full">
        <TabsList className="mb-6">
          <TabsTrigger value="fields">الحقول</TabsTrigger>
          <TabsTrigger value="design">التصميم</TabsTrigger>
          <TabsTrigger value="advanced">إعدادات متقدمة</TabsTrigger>
        </TabsList>

        <TabsContent value="fields" className="space-y-6">
          <div className="bg-gray-50 p-6 rounded-lg">
            <h3 className="text-lg font-semibold mb-4">إعدادات الحقول</h3>
            
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <span>إظهار حقل الاسم</span>
                <Switch
                  checked={settings.showName}
                  onCheckedChange={(checked) => 
                    setSettings(prev => ({ ...prev, showName: checked }))
                  }
                />
              </div>

              <div className="flex items-center justify-between">
                <span>إظهار حقل الهاتف</span>
                <Switch
                  checked={settings.showPhone}
                  onCheckedChange={(checked) => 
                    setSettings(prev => ({ ...prev, showPhone: checked }))
                  }
                />
              </div>

              <div className="flex items-center justify-between">
                <span>إظهار حقل البريد الإلكتروني</span>
                <Switch
                  checked={settings.showEmail}
                  onCheckedChange={(checked) => 
                    setSettings(prev => ({ ...prev, showEmail: checked }))
                  }
                />
              </div>

              <div className="flex items-center justify-between">
                <span>إظهار حقل العنوان</span>
                <Switch
                  checked={settings.showAddress}
                  onCheckedChange={(checked) => 
                    setSettings(prev => ({ ...prev, showAddress: checked }))
                  }
                />
              </div>

              <div className="flex items-center justify-between">
                <span>إظهار حقل المدينة</span>
                <Switch
                  checked={settings.showCity}
                  onCheckedChange={(checked) => 
                    setSettings(prev => ({ ...prev, showCity: checked }))
                  }
                />
              </div>
            </div>

            <div className="mt-6">
              <h4 className="text-md font-semibold mb-3">الحقول المخصصة</h4>
              <Button 
                onClick={handleAddField}
                variant="outline"
                className="w-full justify-center gap-2"
              >
                <Plus className="h-4 w-4" />
                إضافة حقل جديد
              </Button>
            </div>
          </div>

          <div className="bg-gray-50 p-6 rounded-lg">
            <h3 className="text-lg font-semibold mb-4">معاينة مباشرة</h3>
            <div className="h-48 bg-white rounded border border-gray-200">
              {/* Here we'll add the live preview component */}
            </div>
          </div>

          <Button 
            onClick={handleSaveSettings}
            className="w-full justify-center"
          >
            حفظ الإعدادات
          </Button>
        </TabsContent>

        <TabsContent value="design">
          {/* Design settings will be added here */}
          <div className="bg-gray-50 p-6 rounded-lg">
            <h3 className="text-lg font-semibold mb-4">إعدادات التصميم</h3>
            <p className="text-gray-500">سيتم إضافة إعدادات التصميم قريباً</p>
          </div>
        </TabsContent>

        <TabsContent value="advanced">
          {/* Advanced settings will be added here */}
          <div className="bg-gray-50 p-6 rounded-lg">
            <h3 className="text-lg font-semibold mb-4">الإعدادات المتقدمة</h3>
            <p className="text-gray-500">سيتم إضافة الإعدادات المتقدمة قريباً</p>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default SettingsPanel;