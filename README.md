# php-starplot
This is the one decade old QualityPie implementation in PHP

## What?
QualityPie supports the methods recommended in "One-view visualization of speech quality measurement results" (Klemens P. F. Adler, Hans Wilhelm Gierlich and Joachim Pomy (Ed.)., ITU-T Recommendation P.505, 29 November 2005). Further [details](http://www.itu.int/rec/T-REC-P.505-201206-I!Amd1) of [Recommendation P.505 (2005) Amendment 1 (06/12)](http://www.itu.int/rec/dologin_pub.asp?lang=e&id=T-REC-P.505-201206-I!Amd1!PDF-E&type=items) or follow the link to 'Quality Pie' inside the section Special Projects and Issues at the study group [ITU-T SG 12](http://www.itu.int/ITU-T/studygroups/com12/).

Citing from the online tool's informative text:
> The one-view visualization methodology is based on the allocation of individual circle segments to the selected parameters - the so-called "quality pie"; a maximum number of 16 different segments is considered here for practical reasons.
>
>The total number of parameters represented determines the size of the individual segments in the quality pie. The axes are shown with a common origin. The individual circle segments have the same size (spanned angle 360° divided by number of selected quality parameters).
>
>The representation of individual segment sizes is not interdependent, thus guaranteeing the independence of the different quality parameters from each other, which leads to the following advantages:
>
>* Independent representation of individual quality parameters.
>* Segment sizes are determined by the number of selected parameters and are identical.
>* Segment size (radius) is a measure for the quality regarding this parameter.
>* A concentric circle around the origin is defined (1/√2) which represents a minimum quality measure; falling below this segment size (radius) indicates a non-compliance with this limit value.
>* By means of a suitable colour selection results lying within the tolerance or transgressing the limit values can be easily visualized.
>
> This online application of P.505 can help you to produce high quality graphs for your individual set of parameters. It is intended to support the use of this methodology in the field, e.g. for recurring reporting task, but also for benchmarking or for test events.

Source: "QualityPie plot web front-end, Version: 2.0.0, Last update: 2012-05-29"

## Developer Notes
This implementation is not actively supported. But, in case there is a need, an issue in this repository might go a long way ...

**Note**: The default branch is `default`.
