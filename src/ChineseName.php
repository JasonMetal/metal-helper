<?php
/**
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;

/**
 * 数组工具类
 *
 * @package  metal\helper
 */
final class ChineseName
{
    /**
     * @Notes  : 助手 模块
     * ->@Notes  : 获取 姓氏 Surname
     * @param $arr
     * @return :array
     * @user   : XiaoMing
     * @time   : 2020/7/9_11:09
     */
    public static function surname()
    {

        return [

            '赵', '钱', '孙', '李', '周', '吴', '郑', '王', '冯', '陈', '褚', '卫', '蒋', '沈',
            '韩', '杨', '朱', '秦', '尤', '许', '何', '吕', '施', '张', '孔', '曹', '严', '华',
            '金', '魏', '陶', '姜', '戚', '谢', '邹', '喻', '柏', '水', '窦', '章', '云', '苏',
            '潘', '葛', '奚', '范', '彭', '郎', '鲁', '韦', '昌', '马', '苗', '凤', '花', '方',
            '俞', '任', '袁', '柳', '邓', '鲍', '史', '唐', '费', '廉', '岑', '薛', '雷', '贺',
            '倪', '汤', '藤', '殷', '罗', '毕', '郝', '邬', '安', '常', '乐', '于', '时', '付',
            '皮', '卞', '齐', '康', '伍', '余', '元', '卜', '顾', '孟', '平', '黄', '和', '穆',
            '肖', '尹', '姚', '邵', '湛', '汪', '祁', '毛', '禹', '狄', '米', '贝', '明', '藏',
            '计', '伏', '成', '戴', '谈', '宋', '茅', '庞', '熊', '纪', '舒', '屈', '项', '祝',
            '董', '梁', '杜', '阮', '蓝', '闵', '席', '季', '麻', '强', '贾', '路', '娄', '危',
            '江', '童', '颜', '郭', '梅', '盛', '林', '刁', '钟', '徐', '邱', '骆', '高', '夏',
            '蔡', '田', '樊', '胡', '凌', '霍', '虞', '万', '支', '柯', '昝', '管', '卢', '莫',
            '经', '房', '裘', '缪', '干', '解', '应', '宗', '丁', '宣', '贲', '邓', '郁', '单',
            '杭', '洪', '包', '诸', '左', '石', '崔', '吉', '钮', '龚', '程', '嵇', '邢', '滑',
            '裴', '陆', '荣', '翁', '荀', '羊', '于', '惠', '甄', '曲', '家', '封', '芮', '羿',
            '储', '靳', '汲', '邴', '糜', '松', '井', '段', '富', '巫', '乌', '焦', '巴', '弓',
            '牧', '隗', '山', '谷', '车', '侯', '宓', '蓬', '全', '郗', '班', '仰', '秋', '仲',
            '伊', '宫', '宁', '仇', '栾', '暴', '甘', '钭', '厉', '戎', '祖', '武', '符', '刘',
            '景', '詹', '束', '龙', '叶', '幸', '司', '韶', '郜', '黎', '蓟', '薄', '印', '宿',
            '白', '怀', '蒲', '邰', '从', '鄂', '索', '咸', '籍', '赖', '卓', '蔺', '屠', '蒙',
            '池', '乔', '阴', '郁', '胥', '能', '苍', '双', '闻', '莘', '党', '翟', '谭', '贡',
            '劳', '逄', '姬', '申', '扶', '堵', '冉', '宰', '郦', '雍', '隙', '璩', '桑', '桂',
            '濮', '牛', '寿', '通', '边', '扈', '燕', '冀', '郏', '浦', '尚', '农', '温', '别',
            '庄', '晏', '柴', '瞿', '阎', '充', '慕', '连', '茹', '习', '宦', '艾', '鱼', '容',
            '向', '古', '易', '慎', '戈', '廖', '庾', '终', '暨', '居', '衡', '步', '都', '耿',
            '满', '弘', '匡', '文', '国', '寇', '广', '禄', '阙', '东', '欧', '殳', '沃', '利',
            '蔚', '越', '夔', '隆', '师', '巩', '厍', '聂', '晁', '勾', '敖', '融', '冷', '訾',
            '辛', '阚', '那', '简', '饶', '空', '曾', '毋', '沙', '乜', '养', '鞠', '须', '丰',
            '巢', '关', '蒯', '相', '查', '后', '荆', '红', '游', '竺', '权', '逯', '盖', '益',
            '桓', '公', '万', '俟', '司', '马', '上', '官', '欧', '阳', '夏', '候', '诸', '葛',
            '闻', '人', '东', '方', '赫', '连', '皇', '甫', '尉', '迟', '公', '羊', '澹', '台',
            '公', '冶', '宗', '政', '濮', '阳', '淳', '于', '单', '于', '太', '叔', '申', '屠',
            '公', '孙', '仲', '孙', '轩', '辕', '令', '狐', '钟', '离', '宇', '文', '长', '孙',
            '慕', '容', '鲜', '于', '闾', '丘', '司', '徒', '司', '空', '亓', '官', '司', '寇',
            '仉', '督', '子', '车', '颛', '孙', '端', '木', '巫', '马', '公', '西', '漆', '雕',
            '乐', '正', '壤', '驷', '公', '良', '拓', '拔', '夹', '谷', '宰', '父', '谷', '梁',
            '楚', '晋', '闫', '法', '汝', '鄢', '涂', '钦', '段', '干', '百', '里', '东', '郭',
            '南', '门', '呼', '延', '归', '海', '羊', '舌', '微', '生', '岳', '帅', '缑', '亢',
            '况', '后', '有', '琴', '梁', '丘', '左', '丘', '东', '门', '西', '门', '商', '牟',
            '佘', '耳', '伯', '赏', '南', '宫', '墨', '哈', '谯', '笪', '年', '爱', '阳', '佟',

            '欧阳', '太史', '端木', '上官', '司马', '东方', '独孤', '南宫', '万俟', '闻人', '夏侯',
            '诸葛', '尉迟', '公羊', '赫连', '澹台', '皇甫', '宗政', '濮阳', '公冶', '太叔', '申屠',
            '公孙', '慕容', '仲孙', '钟离', '长孙', '宇文', '城池', '司徒', '鲜于', '司空', '汝嫣',
            '闾丘', '子车', '亓官', '司寇', '巫马', '公西', '颛孙', '壤驷', '公良', '漆雕', '乐正',
            '宰父', '谷梁', '拓跋', '夹谷', '轩辕', '令狐', '段干', '百里', '呼延', '东郭', '南门',
            '羊舌', '微生', '公户', '公玉', '公仪', '梁丘', '公仲', '公上', '公门', '公山', '公坚',
            '左丘', '公伯', '西门', '公祖', '第五', '公乘', '贯丘', '公皙', '南荣', '东里', '东宫',
            '仲长', '子书', '子桑', '即墨', '达奚', '褚师',


        ];


    }

    /**
     * @Notes  : 助手 模块
     * ->@Notes  : 获取 xx
     * @return :array
     * @user   : XiaoMing
     * @time   : 2020/7/9_11:10
     */
    public static function nameChars()
    {
        return [
            '嘉', '琼', '桂', '娣', '叶', '璧', '璐', '娅', '琦', '晶', '妍', '茜', '秋', '珊', '莎',
            '锦', '黛', '青', '倩', '婷', '姣', '婉', '娴', '瑾', '颖', '露', '瑶', '怡', '婵', '雁',
            '蓓', '纨', '仪', '荷', '丹', '蓉', '眉', '君', '琴', '蕊', '薇', '菁', '梦', '岚', '苑',
            '婕', '馨', '瑗', '琰', '韵', '融', '园', '艺', '咏', '卿', '聪', '澜', '纯', '毓', '悦',
            '昭', '冰', '爽', '琬', '茗', '羽', '希', '宁', '欣', '飘', '育', '滢', '馥', '筠', '柔',
            '竹', '霭', '凝', '晓', '欢', '霄', '枫', '芸', '菲', '寒', '伊', '亚', '宜', '可', '姬',
            '舒', '影', '荔', '枝', '思', '丽', '秀', '娟', '英', '华', '慧', '巧', '美', '娜', '静',
            '淑', '惠', '珠', '翠', '雅', '芝', '玉', '萍', '红', '娥', '玲', '芬', '芳', '燕', '彩',
            '春', '菊', '勤', '珍', '贞', '莉', '兰', '凤', '洁', '梅', '琳', '素', '云', '莲', '真',
            '环', '雪', '荣', '爱', '妹', '霞', '香', '月', '莺', '媛', '艳', '瑞', '凡', '佳', '涛',
            '昌', '进', '林', '有', '坚', '和', '彪', '博', '诚', '先', '敬', '震', '振', '壮', '会',
            '群', '豪', '心', '邦', '承', '乐', '绍', '功', '松', '善', '厚', '庆', '磊', '民', '友',
            '裕', '河', '哲', '江', '超', '浩', '亮', '政', '谦', '亨', '奇', '固', '之', '轮', '翰',
            '朗', '伯', '宏', '言', '若', '鸣', '朋', '斌', '梁', '栋', '维', '启', '克', '伦', '翔',
            '旭', '鹏', '泽', '晨', '辰', '士', '以', '建', '家', '致', '树', '炎', '德', '行', '时',
            '泰', '盛', '雄', '琛', '钧', '冠', '策', '腾', '伟', '刚', '勇', '毅', '俊', '峰', '强',
            '军', '平', '保', '东', '文', '辉', '力', '明', '永', '健', '世', '广', '志', '义', '兴',
            '良', '海', '山', '仁', '波', '宁', '贵', '福', '生', '龙', '元', '全', '国', '胜', '学',
            '祥', '才', '发', '成', '康', '星', '光', '天', '达', '安', '岩', '中', '茂', '武', '新',
            '利', '清', '飞', '彬', '富', '顺', '信', '子', '杰', '楠', '榕', '风', '航', '弘',
        ];
    }

    public static function randomSurname()
    {
        return self::randomCnName('surname');
    }
    public static function randomNameChars()
    {
        return self::randomCnName('nameChars');
    }
    public static function randomCnName($fun){
        $arrTmp = self::$fun();
        $max = count($arrTmp) - 1;
        return $arrTmp[mt_rand(0,$max)];
    }
    public static function getRandomCnName(){
        return  self::randomSurname() . self::randomNameChars();;
    }
}

